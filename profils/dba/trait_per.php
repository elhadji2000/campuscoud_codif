<?php
session_start();
require_once ('../../traitement/fonction.php');

$connexion = connexionBD();

/* ===============================
   1. Récupération des GET
================================ */
$etu1 = trim($_GET['etu1'] ?? '');
$etu2 = trim($_GET['etu2'] ?? '');

if (empty($etu1) || empty($etu2)) {
    $_SESSION['message'] = 'Deux étudiants requis';
    $_SESSION['message_type'] = 'danger';
    header('Location: permuter.php');
    exit();
}

/* ===============================
   2. Récupération des données
================================ */
$data = getDeuxEtudiantsFull($connexion, $etu1, $etu2);

if (!isset($data[$etu1]) || !isset($data[$etu2])) {
    $_SESSION['message'] = 'Étudiants introuvables';
    $_SESSION['message_type'] = 'danger';
    header('Location: permuter.php?etu1=' . urlencode($etu1) . '&etu2=' . urlencode($etu2));
    exit();
}

$etu1_data = $data[$etu1];
$etu2_data = $data[$etu2];

$id1 = $etu1_data['id_etu'];
$id2 = $etu2_data['id_etu'];

$lit1 = $etu1_data['id_lit'];
$lit2 = $etu2_data['id_lit'];

$id_aff1 = $etu1_data['id_aff'];
$id_aff2 = $etu2_data['id_aff'];

/* ===============================
   3. Vérifications métier
================================ */

// Même sexe
if ($etu1_data['sexe'] !== $etu2_data['sexe']) {
    $_SESSION['message'] = 'Les deux étudiants doivent être du même sexe';
    $_SESSION['message_type'] = 'danger';
    header('Location: permuter.php?etu1=' . urlencode($etu1) . '&etu2=' . urlencode($etu2));
    exit();
}

if ($etu1_data['statut_affectation'] !== $etu2_data['statut_affectation']) {
    $_SESSION['message'] = 'Les deux étudiants doivent être du même statut';
    $_SESSION['message_type'] = 'danger';
    header('Location: permuter.php?etu1=' . urlencode($etu1) . '&etu2=' . urlencode($etu2));
    exit();
}

// Doivent être affectés
if (empty($id_aff1) || empty($id_aff2)) {
    $_SESSION['message'] = 'Les deux étudiants doivent être affectés';
    $_SESSION['message_type'] = 'danger';
    header('Location: permuter.php?etu1=' . urlencode($etu1) . '&etu2=' . urlencode($etu2));
    exit();
}

// Même lit → inutile
if ($lit1 == $lit2) {
    $_SESSION['message'] = 'Les deux étudiants sont déjà dans le même lit';
    $_SESSION['message_type'] = 'warning';
    header('Location: permuter.php?etu1=' . urlencode($etu1) . '&etu2=' . urlencode($etu2));
    exit();
}

/* ===============================
   4. Niveaux
================================ */
$niveau1 = $etu1_data['niveauFormation'];
$niveau2 = $etu2_data['niveauFormation'];

/* ===============================
   5. Lit temporaire sécurisé
================================ */
// $temp_lit = -1;

// S'assurer qu'il existe
/* mysqli_query($connexion, "
    INSERT IGNORE INTO codif_quota (id_lit_q, niveauFormation)
    VALUES ($temp_lit, 'TEMP')
"); */

/* mysqli_query($connexion, "
    INSERT INTO codif_quota (id_lit_q, niveauFormation)
    VALUES ($temp_lit, 'TEMP')
    ON DUPLICATE KEY UPDATE niveauFormation = niveauFormation
"); */

/* ===============================
   6. Transaction
================================ */
mysqli_begin_transaction($connexion);

try {
    /* ===============================
       1. Trouver un lit libre
    =============================== */
    $var = $_SESSION['username'] ." a fait une permutation le " . date('d/m/Y à H:i');
    $res = mysqli_query($connexion, '
        SELECT id_lit_q 
        FROM codif_quota 
        WHERE id_lit_q NOT IN (
            SELECT id_lit FROM codif_affectation
        )
        LIMIT 1
    ');

    $row = mysqli_fetch_assoc($res);

    if (!$row) {
        throw new Exception('Aucun lit libre disponible pour permutation sécurisée');
    }

    $lit_temp = $row['id_lit_q'];

    /* ===============================
       2. Swap sécurisé
    =============================== */

    // etu1 → lit_temp
    $stmt = mysqli_prepare($connexion, '
        UPDATE codif_affectation 
        SET id_lit = ? 
        WHERE id_aff = ?
    ');
    mysqli_stmt_bind_param($stmt, 'ii', $lit_temp, $id_aff1);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // etu2 → lit1
    $stmt = mysqli_prepare($connexion, '
        UPDATE codif_affectation 
        SET id_lit = ? , var = ?
        WHERE id_aff = ?
    ');
    mysqli_stmt_bind_param($stmt, 'isi', $lit1, $var, $id_aff2);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // etu1 → lit2
    $stmt = mysqli_prepare($connexion, '
        UPDATE codif_affectation 
        SET id_lit = ? , var = ?
        WHERE id_aff = ?
    ');
    mysqli_stmt_bind_param($stmt, 'isi', $lit2, $var, $id_aff1);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    mysqli_commit($connexion);

    $_SESSION['message'] = 'Permutation réussie sécurisé';
    $_SESSION['message_type'] = 'success';
} catch (Throwable $e) {
    mysqli_rollback($connexion);

    $_SESSION['message'] = 'Erreur : ' . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
}
/* ===============================
   9. Redirection
================================ */
header('Location: permuter.php?etu1=' . urlencode($etu1) . '&etu2=' . urlencode($etu2));
exit();
?>