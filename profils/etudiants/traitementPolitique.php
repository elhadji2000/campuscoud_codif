<?php session_start();  // Démarre une nouvelle session ou reprend une session existante

if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
require_once('../../traitement/fonction.php');

if (isset($_POST) && count($_POST) > 0) { 
    $idEtu = $_SESSION['id_etu'];
    addPolitiqueConf($idEtu);
    header('Location: resultat.php');
    exit();
}