<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
require_once('../../traitement/fonction.php');
if (!empty($_GET)) { 
    $countError = 0;
    $lastValue = null;
    $_SESSION['erreurLitCodifier'] = '';
    $lastValue = $_GET['lit_selection'];
    $idEtu = $_SESSION['id_etu'];            //echo "."; die;   
    addAffectation($lastValue, $idEtu); 
    header('Location: resultat');
    exit();
} else {
    header('Location: codifier.php?erreurLitCodifier=Veuillez selectionner un lit !');
    exit();
}
