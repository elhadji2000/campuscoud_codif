<?php session_start();


if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}





include('../../traitement/fonction.php');

if (isset($_GET)) {
    $quota = getQuotaClasse($_GET['classe'], $_GET['sexe'])['COUNT(*)'];
    $sexe = $_GET['sexe'];
    $classe = $_GET['classe'];
    header('Location: evolution.php?openModal=getClasse&quota=' . $quota . '&classe=' . $classe . '&sexe=' . $sexe);
}
