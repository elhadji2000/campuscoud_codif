<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
require_once('../../traitement/fonction.php');
if (isset($_GET['id_etu'])) {
    $id_etu = $_GET['id_etu'];
    $delete = delete_choix_lit($id_etu);
    if($delete==1){
        header('Location: codifier.php?success_delete=CHOIX DU LIT ANNULER AVEC SUCCESS !');
        exit();
    }
} else if (isset($_GET['id_etu'])) {
    $id_etu = $_GET['id_etu'];
    $delete = delete_choix_lit($id_etu);
    if($delete==1){
        header('Location: codifier.php?success_delete=CHOIX DU LIT ANNULER AVEC SUCCESS !');
        exit();
    }
} else {
    header('Location: codifier.php?erreurLitCodifier=Veuillez selectionner un lit !');
    exit();
}
