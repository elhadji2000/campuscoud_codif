<?php
session_start();

if (empty($_SESSION['username']) || empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}

require_once ('../../traitement/fonction.php');

/* =====================================================
   1. VERIFICATION ETUDIANT (RECHERCHE)
===================================================== */
if (isset($_POST['numEtudiant'])) {
    $num_etu = trim($_POST['numEtudiant']);
    $_SESSION['num_etu'] = $num_etu;

    //  Vérifier forclusion
    if (getIsForclu($num_etu)) {
        header('Location: clando.php?erreurForclo=Etudiant Forclos(e), donc ne peut pas clandoter !!!');
        exit();
    }

    //  Vérifier existence étudiant
    $student = studentConnect($num_etu);
    if (!$student) {
        header('Location: clando.php?erreurNonTrouver=ETUDIANT INTROUVABLE !!!');
        exit();
    }

    //  Calcul statut
    $classe = $student['niveauFormation'];
    $sexe = $student['sexe'];

    $quota = getQuotaClasse($classe, $sexe)['COUNT(*)'];
    $statutData = getOnestudentStatus($quota, $classe, $sexe, $num_etu);

    if ($statutData['statut'] !== 'Attributaire') {
        header('Location: clando.php?erreurNonTrouver=ETUDIANT NON ATTRIBUTAIRE !!!');
        exit();
    }

    //  Vérifier paiement
    $data = getOneByValidatePaiement($num_etu, $_SESSION['pavillon']);

    if (mysqli_num_rows($data) === 0) {
        header('Location: clando.php?erreurNonTrouver=ETUDIANT NON RESIDENT DU PAVILLON !!!');
        exit();
    }

    $row = mysqli_fetch_assoc($data);

    if (empty($row)) {
        header('Location: clando.php?erreurValider=VEUILLEZ PROCEDER AU PAIEMENT DABORD !!!');
        exit();
    }

    //  Cas non logé
    if ($row['etat_id_paie'] === 'Non migré ass') {
        header('Location: clando.php?erreurValider=ETUDIANT AYANT PAYE MAIS NON LOGE !!!');
        exit();
    }

    //  OK
    $queryString = http_build_query(['data' => $row]);
    header('Location: clando.php?success=ETUDIANT PEUT CLANDOTER&' . $queryString);
    exit();
}

/* =====================================================
   2. VALIDATION CLANDO
===================================================== */
if (isset($_POST['id_paie'])) {
    try {
        $num_etu = trim($_POST['num_etu_clando']);

        //  Vérifier statut NON ATTRIBUTAIRE
        $student = studentConnect($num_etu);

        if (!$student) {
            $api = getDonneesEtudiant_2($num_etu);
            if ($api) {
                $student = [
                    'nom' => $api['nom'],
                    'prenoms' => $api['prenom'],
                    'dateNaissance' => $api['date_naissance'],
                    'telephone' => $api['telephone'],
                    'email_ucad' => $api['email_ucad'],
                    'lieuNaissance' => $api['lieu_naissance'],
                    'etablissement' => $api['faculte'],
                    'departement' => $api['departement'],
                    'niveauFormation' => $api['niveau_formation'],
                    'sexe' => $api['sexe'],
                    'annee' => $api['annee'],
                    'numIdentite' => $api['num_identite']
                ];
                $nom = $student['nom'];
                $prenoms = $student['prenoms'];
                $dateNaissance = $student['dateNaissance'];
                $lieuNaissance = $student['lieuNaissance'];
                $sexe = $student['sexe'];
                $nationalite = $student['nationalite'];
                $numIdentite = $student['numIdentite'];
                $telephone = $student['telephone'];
                $etablissement = $student['etablissement'];
                $departement = $student['departement'];
                $niveauFormation = $student['niveauFormation'];
                $moyenne = 1;

                $anneeEtudiant = $student['annee'];  // ex: 2024_2025
                $anneeFin = intval(substr($anneeEtudiant, -4));  // 2025
                $annee = isset($_SESSION['annee']) ? $_SESSION['annee'] : date('Y');
                $anneeCourante = intval(substr($annee, -4));

                if (($anneeCourante - $anneeFin) >= 2) {
                    header('Location: clando.php?erreurValider=ETUDIANT Non Inscrit(e) !!!');
                    exit();
                }

                $resulte_1 = enregistrerEtudiant($connexion, $num_etu, $prenoms, $nom, $telephone, $lieuNaissance, $dateNaissance, $etablissement, $departement, $niveauFormation, $moyenne, $numIdentite, $sexe);
            } else {
                header('Location: clando.php?erreurValider=ETUDIANT INTROUVABLE !!!');
                exit();
            }
        }

        $classe = $student['niveauFormation'];
        $sexe = $student['sexe'];

        $quota = getQuotaClasse($classe, $sexe)['COUNT(*)'];
        $statutData = getOnestudentStatus($quota, $classe, $sexe, $num_etu);

        if (($statutData['statut'] == 'Attributaire') || ($statutData['statut'] == 'Suppleant(e)')) {
            header('Location: clando.php?erreurValider=Seuls les NON ATTRIBUTAIRES peuvent etre clandotés !!!');
            exit();
        }

        //  Infos
        $id_etu = info($num_etu)[15];
        $id_paie = $_POST['id_paie'];
        $user = $_SESSION['username'];

        //  Insertion
        $result = setLogerClando($id_paie, $user, $id_etu);

        if ($result == 1) {
            header('Location: clando.php?successValider=Hebergement effectué avec succès !!!');
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        header('Location: clando.php?erreurValider=Etudiant deja logé !!!');
        exit();
    }
}