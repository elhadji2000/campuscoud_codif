<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Démarrer la session pour stocker les messages
 include('../../traitement/fonction.php'); 

 if (isset($_POST) && count($_POST) > 0) {
    if (isset($_POST['nature'])) {
        $nature = $_POST['nature'];
        if (isset($_POST['date'])) {
            $date = $_POST['date'];
            if (isset($_POST['faculte'])) {
                $faculte = $_POST['faculte'];            
                $allMessages = [];
                foreach ($faculte as $fac) {
                    // Appeler la fonction pour valider les dates
                    $messages = validate_date_limite_codif_delai($fac, $nature, $date);
                    $allMessages = array_merge($allMessages, $messages); // Ajouter les messages à un tableau global
                }

                // // Afficher les messages (succès ou erreur)
                // foreach ($allMessages as $message) {
                //     echo "<p>$message</p>";
                // }

                // Enregistrer les messages dans la session
                $_SESSION['all_messages'] = $allMessages;

                // Rediriger l'utilisateur vers le formulaire avec les messages
                header("Location: add_delai.php");
                exit;
            }
        }
    }
}


?>