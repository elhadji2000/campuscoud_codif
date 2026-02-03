<?php
session_start();
require_once ('../../traitement/fonction.php');

$numTitulaire = trim($_POST['num_titulaire'] ?? '');
$numSuppleant = trim($_POST['num_suppleant'] ?? '');

if (!$numTitulaire || !$numSuppleant) {
    die('Numéros étudiants manquants');
}

$username = $_SESSION['username'] ?? 'system';

mysqli_begin_transaction($connexion);

try {
    /* ===============================
       1. TITULAIRE (obligatoire)
       =============================== */

    $stmt = mysqli_prepare($connexion,
        'SELECT * FROM codif_etudiant WHERE num_etu = ?');
    mysqli_stmt_bind_param($stmt, 's', $numTitulaire);
    mysqli_stmt_execute($stmt);
    $titulaire = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$titulaire) {
        throw new Exception('Titulaire introuvable');
    }

    $idTitulaire = $titulaire['id_etu'];
    $ancienTitulaire = $titulaire;

    /* ===============================
       2. SUPPLÉANT
       =============================== */

    $stmt = mysqli_prepare($connexion,
        'SELECT * FROM codif_etudiant WHERE num_etu = ?');
    mysqli_stmt_bind_param($stmt, 's', $numSuppleant);
    mysqli_stmt_execute($stmt);
    $suppleantDB = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($suppleantDB) {
        // ➜ suppression du suppléant existant
        $stmt = mysqli_prepare($connexion,
            'DELETE FROM codif_etudiant WHERE id_etu = ?');
        mysqli_stmt_bind_param($stmt, 'i', $suppleantDB['id_etu']);
        mysqli_stmt_execute($stmt);

        $suppleant = $suppleantDB;
    } else {
        // ➜ récupération via API
        $api = getDonneesEtudiant_2($numSuppleant);
        if (!$api) {
            throw new Exception('Suppléant introuvable (API)');
        }

        $suppleant = [
            'num_etu' => $numSuppleant,
            'nom' => $api['nom'],
            'prenoms' => $api['prenom'],
            'dateNaissance' => $api['date_naissance'],
            'telephone' => $api['telephone'],
            'email_ucad' => $api['email_ucad'],
            'lieuNaissance' => $api['lieu_naissance'],
            'sexe' => $api['sexe'],
            'numIdentite' => $api['num_identite']
        ];
    }

    /* ===============================
       3. PERMUTATION
       =============================== */

    /* ===============================
    3. PERMUTATION (UPDATE COMPLET)
    =============================== */
    // ==== NORMALISATION DES DONNÉES SUPPLÉANT ==== //
    $numEtu = $suppleant['num_etu'];
    $nom = $suppleant['nom'];
    $prenoms = $suppleant['prenoms'];

    $dateNaissance = $suppleant['dateNaissance'];
    $lieuNaissance = $suppleant['lieuNaissance'];
    $sexe = $suppleant['sexe'];
    $nationalite = $suppleant['nationalite'];
    $numIdentite = $suppleant['numIdentite'];
    $telephone = $suppleant['telephone'];
    $emailPerso = $suppleant['email_perso'];
    $emailUcad = $suppleant['email_ucad'];
    $departement = $titulaire['departement'];
    $etablissement = $titulaire['etablissement'];
    $niveauFormation = $titulaire['niveauFormation'];
    $typeEtudiant = $titulaire['typeEtudiant'];
    $var = $_SESSION['username'] . " a fait l'objet d'un remplacement le " . date('d/m/Y à H:i');

    $stmt = mysqli_prepare($connexion,
        'UPDATE codif_etudiant SET
        num_etu = ?,
        nom = ?,
        prenoms = ?,
        dateNaissance = ?,
        lieuNaissance = ?,
        sexe = ?,
        nationalite = ?,
        numIdentite = ?,
        telephone = ?,
        email_perso = ?,
        email_ucad = ?,
        departement = ?,
        etablissement = ?,
        niveauFormation = ?,
        typeEtudiant = ?
        var = ?
     WHERE id_etu = ?');

    mysqli_stmt_bind_param(
        $stmt,
        'ssssssssisssssssi',
        $numEtu,
        $nom,
        $prenoms,
        $dateNaissance,
        $lieuNaissance,
        $sexe,
        $nationalite,
        $numIdentite,
        $telephone,
        $emailPerso,
        $emailUcad,
        $departement,
        $etablissement,
        $niveauFormation,
        $typeEtudiant,
        $var,
        $idTitulaire
    );

    mysqli_stmt_execute($stmt);

    /* ===============================
       4. RÉINSERTION ANCIEN TITULAIRE
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
    $moyenne, //  OK
    $ancienTitulaire['sessionId'],
    $ancienTitulaire['niveau'],
    $ancienTitulaire['email_perso'],
    $ancienTitulaire['email_ucad'],
    $ancienTitulaire['telephone'],
    $ancienTitulaire['var']
);

    mysqli_stmt_execute($stmt);

    $newIdAncien = mysqli_insert_id($connexion);

    /* ===============================
       5. FORCLUSION
       =============================== */

    $stmt = mysqli_prepare($connexion,
        "INSERT INTO codif_forclusion
         (id_etu, dateTime_for, type, motif_manuel, username_user)
         VALUES (?, NOW(), 'Manuel', 'remplacement', ?)");
    mysqli_stmt_bind_param($stmt, 'is', $newIdAncien, $username);
    mysqli_stmt_execute($stmt);

    /* ===============================
       6. COMMIT
       =============================== */

    mysqli_commit($connexion);

    $_SESSION['message'] = 'Permutation effectuée avec succès';
    $_SESSION['message_type'] = 'success';
} catch (Exception $e) {
    mysqli_rollback($connexion);

    $_SESSION['message'] = 'Erreur : ' . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
}

header('Location: etudiant.php');
exit();