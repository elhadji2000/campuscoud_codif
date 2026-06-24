<?php
session_start();

include ('../../traitement/fonction.php');
include ('fonction2.php');

verif_type_mdp_2($_SESSION['username']);

$fac = $_GET['fac'] ?? null;

if (!$fac) {
    $_SESSION['message'] = 'Faculté non définie';
    $_SESSION['message_type'] = 'danger';
    header('Location: nonAffect');
    exit;
}

/* ===============================
   1. Récupération des données
=============================== */
$result2 = getAttributaireAndSuppleantByFac($fac);
$lits = getLitNonAffByFac($fac);

/* ===============================
   2. Extraire les suppléants
=============================== */
$suppleants = [];
$connexion_2 = $connexion;
foreach ($result2 as $row) {
    if (!empty($row['suppleant'])) {
        $suppleants[] = [
            'id_etu' => $row['suppleant']['id_etu'],
            'num_etu' => $row['suppleant']['num_etu'],
            'classe' => $row['suppleant']['classe'],
            'sexe' => $row['suppleant']['sexe'], //  IMPORTANT
            'titulaire_id' => $row['titulaire']['id_etu']
        ];
    }else {
        $stmt = $connexion_2->prepare("
                INSERT INTO codif_forclusion 
                (id_etu, dateTime_for, type, motif_manuel, username_user)
                VALUES (?, NOW(), 'Manuel', 'remplacement', ?)
            ");
            $stmt->bind_param('is', $row['titulaire']['id_etu'], $_SESSION['username']);
            $stmt->execute();
            $stmt->close();
    }
}

/* ===============================
   3. Vérification
=============================== */
if (empty($lits) || empty($suppleants)) {
    $_SESSION['message'] = 'Aucune affectation possible';
    $_SESSION['message_type'] = 'warning';
    header('Location: nonAffect?fac=' . urlencode($fac));
    exit;
}

mysqli_begin_transaction($connexion);

try {

    $litsParNiveau = [];

    /* ===============================
       4. BOUCLE PRINCIPALE (LIT → ETUDIANT)
    =============================== */
    foreach ($lits as $keyLit => $lit) {

        $idLit = $lit['id_lit'];
        $numeroLit = $lit['lit'];
        $sexeLit = $lit['sexe']; // IMPORTANT

        /* ===============================
           Vérifier si lit déjà affecté
        =============================== */
        $check = $connexion->prepare('SELECT id_lit FROM codif_affectation WHERE id_lit = ?');
        $check->bind_param('i', $idLit);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $check->close();
            continue;
        }
        $check->close();

        /* ===============================
           Chercher étudiant compatible
        =============================== */
        foreach ($suppleants as $keySup => $suppleant) {

            if ($suppleant['sexe'] !== $sexeLit) {
                continue;
            }

            $idSuppleant = $suppleant['id_etu'];
            $niveauFormation = $suppleant['classe'];
            $idTitulaire = $suppleant['titulaire_id'];
            $num_etu = $suppleant['num_etu'];

            /* ===============================
               1. Affectation
            =============================== */
            if (!addAffectation($idLit, $idSuppleant)) {
                throw new Exception("Erreur affectation ETUDIANT $idSuppleant");
            }

            /* ===============================
               2. Récupérer id_aff
            =============================== */
            $sql = 'SELECT id_aff 
                    FROM codif_affectation 
                    WHERE id_lit = ? AND id_etu = ?
                    ORDER BY id_aff DESC LIMIT 1';

            $stmt = $connexion->prepare($sql);
            $stmt->bind_param('ii', $idLit, $idSuppleant);
            $stmt->execute();

            $res = $stmt->get_result();
            $rowAff = $res->fetch_assoc();
            $stmt->close();

            if (!$rowAff) {
                throw new Exception('Affectation non retrouvée');
            }

            $id_aff = $rowAff['id_aff'];
            $id_user = $_SESSION['id_user'];

            /* ===============================
               3. Validation + SMS
            =============================== */
            if (!setValidation($id_aff, $id_user)) {
                throw new Exception('Erreur validation');
            }

            sms_nv_attributaire($num_etu);

            /* ===============================
               4. Mise à jour étudiant
            =============================== */
            $nouveauNiveau = $niveauFormation . '_' . $numeroLit.' CLT';

            $stmt = $connexion->prepare('
                UPDATE codif_etudiant 
                SET niveauFormation = ? 
                WHERE id_etu = ?
            ');
            $stmt->bind_param('si', $nouveauNiveau, $idSuppleant);
            $stmt->execute();
            $stmt->close();

            /* ===============================
               5. Quota
            =============================== */
            updatequota($connexion, $idLit, $nouveauNiveau);

            /* ===============================
               6. Forclusion titulaire
            =============================== */
            $stmt = $connexion->prepare("
                INSERT INTO codif_forclusion 
                (id_etu, dateTime_for, type, motif_manuel, username_user)
                VALUES (?, NOW(), 'Manuel', 'remplacement', ?)
            ");
            $stmt->bind_param('is', $idTitulaire, $_SESSION['username']);
            $stmt->execute();
            $stmt->close();

            /* ===============================
               7. Tracking
            =============================== */
            if (!isset($litsParNiveau[$niveauFormation])) {
                $litsParNiveau[$niveauFormation] = '';
            }

            $litsParNiveau[$niveauFormation] .= $numeroLit . ', ';

            /* ===============================
                IMPORTANT
            =============================== */

            //  Supprimer étudiant utilisé
            unset($suppleants[$keySup]);

            //  passer au lit suivant
            break;
        }
    }

    /* ===============================
       Nettoyage
    =============================== */
    foreach ($litsParNiveau as $niveau => $chaine) {
        $litsParNiveau[$niveau] = rtrim($chaine, ', ');
    }

    mysqli_commit($connexion);

    $_SESSION['message'] = 'Affectation réussie';
    $_SESSION['message_type'] = 'success';

} catch (Exception $e) {

    mysqli_rollback($connexion);

    $_SESSION['message'] = 'Erreur : ' . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
}

/* ===============================
   Redirection
=============================== */
header('Location: nonAffect?fac=' . urlencode($fac));
exit;