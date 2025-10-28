<?php
// Démarrer la session pour stocker les messages
session_start();
include('../../traitement/fonction.php');

$data_all_quota_F = getQuotaClasse($_SESSION['classe'], 'F');
if (isset($_GET['id_lit_q'])) {
    $delete = removeQuotas($_GET['id_lit_q']);
    if ($delete) {
        header("Location: auditQuota.php?message=LIT RETIRER DU QUOTA AVEC SUCCESS");
        exit;
    }
}

if (isset($_POST['niveauFormation'])) {
    try{
        $insert = addDemarrage($_POST['niveauFormation'], $_SESSION['username'], $_SESSION['sexe_agent']);
        header("Location: auditQuota.php?message2=QUOTA ENREGISTRER AVEC SUCCESS");
        exit;
    }catch(Exception $e){
        header("Location: auditQuota.php?message3=Oups =>Erreur : ".$e->getMessage());
        exit;
    }
}
