<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
if (isset($_SESSION['classe'])) {
    $classe = $_SESSION['classe'];
} else {
    $classe = "";
}
include('../../traitement/fonction.php');

if($_SERVER["REQUEST_METHOD"] == "POST"){
if (isset($_POST['rechercher'])) {
      // Récupérer les dates du formulaire
      $date_debut = $_SESSION['debut'] = $_POST['date_debut'];
      $date_fin = $_SESSION['fin'] = $_POST['date_fin'];
	  $username=$_SESSION['username'];

     // if (!empty($date_debut) && !empty($date_fin)){ 
          $tabPaiment[] = getPaiementWithDateInterval($date_debut, $date_fin,$username);
          if ($tabPaiment == null) {
            header('Location: etatPaiement.php?message= Aucun resultat trouvé');
            exit();
        }
        else {  
            $queryString = http_build_query(['data' => $tabPaiment]);
            header('Location: etatPaiement.php?' . $queryString);
           
            exit();
        }
      //}
    }

    elseif (isset($_POST['imprimer'])) {
        header('Location: convention/paiementPdf.php?'); 
    }
}




