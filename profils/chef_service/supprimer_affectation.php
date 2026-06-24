<?php
session_start();
include('../../traitement/fonction.php');
global $connexion;

$lit = $_GET['lit'] ?? null;
$id_aff = $_GET['id_aff'] ?? null;
$type = $_GET['type'] ?? null;
$fac  = $_SESSION['fac']; 
$niveauFormation = $fac . "/SOCIALE";

if (isAffectationValidee($id_aff)) {
    $_SESSION['message'] = "Impossible de supprimer : cette affectation est déjà validée.";
    $_SESSION['message_type'] = "danger";
    header("Location: pageLit?lit=" . $_GET['lit']);
    exit();
}


if (!$lit || !$id_aff || !$type) {
    $_SESSION['message'] = "Paramètres invalides.";
    $_SESSION['message_type'] = "danger";
    header("Location: lit?lit=$lit");
    exit();
}

// Récupérer l’affectation
$aff = getAffectationById($id_aff); 
$idEtu = $aff['id_etu'];
$idLit = $aff['id_lit'];

// SUPPRESSION SUPPLÉANT
if ($type === "suppleant") {

    deleteAffectation2($id_aff);

    // Mise à jour étudiant
    resetEtudiantNiveauFormation($idEtu);

    $_SESSION['message'] = "Suppléant supprimé avec succès.";
    $_SESSION['message_type'] = "success";
    header("Location: pageLit?lit=$lit");
    exit();
}


// SUPPRESSION TITULAIRE
if ($type === "titulaire") {

    // Récupérer tous les occupants
    $occupants = getOccupantsByLit($lit);

    // 1. Supprimer le suppleant si existe
    if (count($occupants) == 2) {
        deleteAffectation2($occupants[1]['id_aff']);
        resetEtudiantNiveauFormation($occupants[1]['id_etu']);
    }

    // 2. Supprimer le titulaire
    deleteAffectation2($id_aff);
    resetEtudiantNiveauFormation($idEtu);

    // 3. Réinitialiser quota lit
    updatequota($connexion, $idLit, $niveauFormation);

    $_SESSION['message'] = "Titulaire supprimé avec succès (et suppléant si existant).";
    $_SESSION['message_type'] = "success";
    header("Location: pageLit?lit=$lit");
    exit();
}
