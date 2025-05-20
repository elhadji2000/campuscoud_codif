<?php

include( '../../traitement/fonction.php' );

session_start(); // Démarrer la session au début

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rechercher'])) {
    // Récupération des champs
    $date_debut = urlencode($_POST['date_debut']);
    $date_fin = urlencode($_POST['date_fin']);
    $username = urlencode($_POST['regisseur']);
    $libelle = urlencode($_POST['libelle']);

    $_SESSION['debut'] = $date_debut;
    $_SESSION['fin'] = $date_fin;
    $_SESSION['regisseur'] = $username;
    $_SESSION['libelle'] = $libelle;

    // Redirection vers la même page avec paramètres GET
    header("Location: etatPaiement_cs.php?date_debut=$date_debut&date_fin=$date_fin&regisseur=$username&libelle=$libelle&page=1");
    exit();
}

    
    // ##################### POUR IMPRIMER ###########################
 /*    elseif (isset($_POST['imprimer'])) {
        // Vérifier si les valeurs sont déjà en session
        if (isset($_SESSION['debut'], $_SESSION['fin'], $_SESSION['regisseur'], $_SESSION['libelle'])) {
            $date_debut = $_SESSION['debut'];
            $date_fin = $_SESSION['fin'];
            $username = $_SESSION['regisseur'];
            $libelle = $_SESSION['libelle'];

            $tabPaiment = getPaiementWithDateInterval_2($date_debut, $date_fin, $username, $libelle);
            
            if ($tabPaiment == null) {
                header('Location: convention/paiementPdf.php?message=Aucun resultat trouvé');
                exit();
            } else {
                $_SESSION['pdf'] = $tabPaiment;
                header('Location: convention/paiementPdf.php');
                exit();
            }
        } else {
            // Redirection si la session est vide
            header('Location: etatPaiement_cs.php?message=Veuillez d\'abord rechercher avant d\'imprimer');
            exit();
        }
    } */
?>

