<?php
session_start();
require_once ('../../traitement/fonction.php');

$numero = trim($_GET['numero'] ?? '');
$id_etu = trim($_GET['id_etu'] ?? '');
$id_lit = intval(trim($_GET['id_lit'] ?? ''));
$newClasse = $etudiant['niveauFormation'].' CLT';

/* ===============================
   1. Vérifier si numero existe
   =============================== */
if (empty($numero)) {
    $_SESSION['message'] = 'Numéro étudiant manquant';
    $_SESSION['message_type'] = 'danger';
    header('Location: remplacer.php');
    exit();
}

/* ===============================
   2. Récupérer étudiant via API / fonction
   =============================== */
$etudiant = studentConnect($numero);
$newClasse = $etudiant['niveauFormation'].' CLT';
/* ===============================
   3. Vérifier si étudiant trouvé
   =============================== */
if (empty($etudiant) || !is_array($etudiant)) {
    $_SESSION['message'] = 'Étudiant introuvable';
    $_SESSION['message_type'] = 'danger';
    header('Location: remplacer.php');
    exit();
}

/* ===============================
   4. Vérifier si id_lit existe (important pour ton traitement)
   =============================== */
if (empty($id_lit)) {
    $_SESSION['message'] = 'Identifiant titulaire manquant';
    $_SESSION['message_type'] = 'danger';
    header('Location: remplacer.php');
    exit();
}

// Récupérer le quota actuel
$quotaRow = getQuotaClasse($etudiant['niveauFormation'], $etudiant['sexe']);
$quota = intval($quotaRow['COUNT(*)']);

// Statut et rang
$dataStatutStudent = getOnestudentStatus($quota, $etudiant['niveauFormation'], $etudiant['sexe'], $numero);
$rangTitulaire = intval($dataStatutStudent['rang']);

// Suppléant
$suppleant = getOneSuppleantByTitulaire($quota, $etudiant['niveauFormation'], $etudiant['sexe'], $rangTitulaire);
$ancienTitulaire = $etudiant;
if (empty($suppleant)) {
    $_SESSION['message'] = 'suppleant manquant';
    $_SESSION['message_type'] = 'danger';
    header('Location: remplacer.php');
    exit();
}
mysqli_begin_transaction($connexion);
try {
    /* ===============================
      1. Mettre à jour le niveauFormation du titulaire
   =============================== */
    $stmt = mysqli_prepare($connexion, '
        UPDATE codif_etudiant 
        SET niveauFormation = ? 
        WHERE id_etu = ?
    ');
    mysqli_stmt_bind_param($stmt, 'si', $newClasse, $id_etu);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    /* ===============================
       2. Mettre à jour le quota correspondant
       (on suppose qu'il y a une seule ligne par classe et sexe)
    =============================== */
    $stmt = mysqli_prepare($connexion, '
        UPDATE codif_quota
        SET niveauFormation = ?
        WHERE id_lit_q = ? 
        LIMIT 1
    ');
    mysqli_stmt_bind_param($stmt, 'si', $newClasse, $id_lit);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    /* ===============================
       1. Récupérer le titulaire actuel
    =============================== */
    $etudiant = studentConnect($numero);
    $idTitulaire = intval($etudiant['id_etu']);

    /* ===============================
       2. Vérifier si le suppléant existe en DB
    =============================== */

    if ($suppleant) {
        // Supprimer l'entrée existante
        $stmt = mysqli_prepare($connexion, 'DELETE FROM codif_etudiant WHERE id_etu = ?');
        mysqli_stmt_bind_param($stmt, 'i', $suppleant['id_etu']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // $suppleant = $suppleantDB;
    } else {
        $_SESSION['message'] = 'suppleant introuvable';
        $_SESSION['message_type'] = 'danger';
        header('Location: remplacer.php');
        exit();
    }

    /* ===============================
       3. Mise à jour du titulaire avec le suppléant
    =============================== */
    $stmt = mysqli_prepare($connexion, '
        UPDATE codif_etudiant SET
            num_etu = ?, nom = ?, prenoms = ?, dateNaissance = ?, lieuNaissance = ?,
            sexe = ?, nationalite = ?, numIdentite = ?, telephone = ?, email_perso = ?,
            email_ucad = ?, departement = ?, etablissement = ?, niveauFormation = ?, typeEtudiant = ?, var = ?
        WHERE id_etu = ?
    ');

    $var = $_SESSION['username'] . ' a fait un remplacement le ' . date('d/m/Y à H:i');

    $num_etu = $suppleant['num_etu'];
    $nom = $suppleant['nom'];
    $prenoms = $suppleant['prenoms'];
    $dateNaissance = $suppleant['dateNaissance'];
    $lieuNaissance = $suppleant['lieuNaissance'];
    $sexe = $suppleant['sexe'];
    $nationalite = $suppleant['nationalite'] ?? '';
    $numIdentite = $suppleant['numIdentite'];
    $telephone = $suppleant['telephone'];
    $email_perso = $suppleant['email_perso'] ?? '';
    $email_ucad = $suppleant['email_ucad'];
    $departement = $etudiant['departement'];
    $etablissement = $etudiant['etablissement'];
    $niveauFormation = $newClasse;
    $typeEtudiant = $etudiant['typeEtudiant'];
    $var = $_SESSION['username'] . ' a fait un remplacement le ' . date('d/m/Y à H:i');
    $idTitulaire = (int) $idTitulaire;

    mysqli_stmt_bind_param(
        $stmt,
        'ssssssssssssssssi',
        $num_etu,
        $nom,
        $prenoms,
        $dateNaissance,
        $lieuNaissance,
        $sexe,
        $nationalite,
        $numIdentite,
        $telephone,
        $email_perso,
        $email_ucad,
        $departement,
        $etablissement,
        $niveauFormation,  // niveau titulaire
        $typeEtudiant,
        $var,
        $idTitulaire
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    /* ===============================
       5. Réinsertion de l’ancien titulaire en tant que suppléant
    =============================== */
    $stmt = mysqli_prepare($connexion, '
        INSERT INTO codif_etudiant (
            etablissement, departement, niveauFormation,
            num_etu, nom, prenoms, dateNaissance,
            lieuNaissance, sexe, nationalite,
            numIdentite, typeEtudiant, moyenne,
            sessionId, niveau, email_perso,
            email_ucad, telephone, var
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ');

    $moyenne = 10;

    mysqli_stmt_bind_param(
        $stmt,
        'sssssssssssssisisss',
        $ancienTitulaire['etablissement'],
        $ancienTitulaire['departement'],
        $ancienTitulaire['niveauFormation'],
        $ancienTitulaire['num_etu'],
        $ancienTitulaire['nom'],
        $ancienTitulaire['prenoms'],
        $ancienTitulaire['dateNaissance'],
        $ancienTitulaire['lieuNaissance'],
        $ancienTitulaire['sexe'],
        $ancienTitulaire['nationalite'],
        $ancienTitulaire['numIdentite'],
        $ancienTitulaire['typeEtudiant'],
        $moyenne,
        $ancienTitulaire['sessionId'],
        $ancienTitulaire['niveau'],
        $ancienTitulaire['email_perso'],
        $ancienTitulaire['email_ucad'],
        $ancienTitulaire['telephone'],
        $ancienTitulaire['var']
    );

    mysqli_stmt_execute($stmt);
    $newIdAncien = mysqli_insert_id($connexion);
    mysqli_stmt_close($stmt);

    /* ===============================
       6. Traçabilité : forclusion
    =============================== */
    $stmt = mysqli_prepare($connexion,
        'INSERT INTO codif_forclusion (id_etu, dateTime_for, type, motif_manuel, username_user)
         VALUES (?, NOW(), "Manuel", "remplacement", ?)');
    mysqli_stmt_bind_param($stmt, 'is', $newIdAncien, $_SESSION['username']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    mysqli_commit($connexion);

    $_SESSION['message'] = 'Tache effectuée avec succès';
    $_SESSION['message_type'] = 'success';
   // sms_nv_attributaire('20240CXQN'); 
} catch (Exception $e) {
    mysqli_rollback($connexion);
    $_SESSION['message'] = 'Erreur : ' . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
}

header('Location: remplacer.php');
exit();
?>