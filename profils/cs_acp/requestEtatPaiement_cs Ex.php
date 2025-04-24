<?php 

include('../../traitement/fonction.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['rechercher'])) {
        // Récupérer les données du formulaire
        $date_debut = $_POST['date_debut'];
        $date_fin = $_POST['date_fin'];
        $username = $_POST['regisseur'];
        $libelle = $_POST['libelle'];

       // if (!empty($date_debut) && !empty($date_fin)) { 
            $tabPaiment = getPaiementWithDateInterval_2( $date_debut, $date_fin, $username, $libelle );
            if ($tabPaiment == null) {
                header('Location: etatPaiement_cs.php?message=Aucun resultat trouvé');
                exit();
            } else {
                // Stocker les données dans une session
                session_start();
                $_SESSION['data'] = $tabPaiment;

                // Redirection vers la page cible
                header('Location: etatPaiement_cs.php');
                exit();
            }
       // }
    }
}




