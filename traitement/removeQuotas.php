<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /codif');
    exit();
}
require_once('fonction.php');
if (isset($_GET) && count($_GET) > 0) {
    $_SESSION['erreurLitDeaffecter'] = '';
    // Parcourir le tableau associatif pour récupérer les ID des boutons sélectionnés
    $countError = 0;
    foreach ($_GET as $buttonId => $value) {
        if ($value === "on") {
            removeQuotas($buttonId);
        } else {
            $countError++;
        }
    }
    if ($countError == 0) {
        return header('Location: ../profils/personnels/detailsLits.php?successLitDeaffecter=Lits retiré du quota de la : ' . $_SESSION['classe']);
        exit();
    }
} else {
    header('Location: ../profils/personnels/detailsLits.php?erreurLitDeaffecter=VEUILLER SELECTIONNER UN LIT !!!');
    exit();
}
// require_once('close.php');
