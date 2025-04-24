<?php

include( '../../traitement/fonction.php' );

session_start(); // Démarrer la session au début

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['rechercher'])) {
        // Récupérer les données du formulaire
        $date_debut = $_POST['date_debut'];
        $date_fin = $_POST['date_fin'];
        $username = $_POST['regisseur'];
        $libelle = $_POST['libelle'];

        // Stocker les données dans la session
        $_SESSION['debut'] = $date_debut;
        $_SESSION['fin'] = $date_fin;
        $_SESSION['regisseur'] = $username;
        $_SESSION['libelle'] = $libelle;

        // Rechercher les paiements
        $tabPaiment = getPaiementWithDateInterval_2($date_debut, $date_fin, $username, $libelle);
        
        if ($tabPaiment == null) {
            header('Location: etatPaiement_cs.php?message=Aucun resultat trouvé');
            exit();
        } else {
            $_SESSION['data'] = $tabPaiment;
            header('Location: etatPaiement_cs.php');
            exit();
        }
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
}


