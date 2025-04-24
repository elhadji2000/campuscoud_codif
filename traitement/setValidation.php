<?php session_start();
// Démarre une nouvelle session ou reprend une session existante

if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
require_once('fonction.php');
if (isset($_GET) && count($_GET) > 0) {
    $_SESSION['erreurValider'] = '';
    $countError = 0;
    // Parcourir le tableau associatif pour récupérer les ID des boutons sélectionnés
    foreach ($_GET as $buttonId => $value) {
        if ($value === "on") {
            setValidation($buttonId, $_SESSION['username']);
        } else {
            $countError++;
        }
    }
    if ($countError == 0) {
        header('Location: ../profils/personnels/validation.php');
        exit();
    }
} else {
    header('Location: ../profils/personnels/validation.php?erreurValider=VEUILLER CHOISIR UN LIT !!!');
    exit();
}
