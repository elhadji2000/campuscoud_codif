<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();  // Démarrer la session pour stocker les messages
include ('../../traitement/fonction.php');

$num_etu        = 'XXXXXX';
$prenoms        = 'XXXXXX';
$nom            = 'XXXXXX';
$telephone      = 'XXXXXX';
$lieuNaissance  = 'XXXXXX';
$dateNaissance = date('Y-m-d');
$moyenne        = 5;
$numIdentite    = 'XXXXXX';
$sexe           = 'F';

if (isset($_POST) && count($_POST) > 0) {
    if (isset($_POST['nature'])) {
        $nature = $_POST['nature'];
        if (isset($_POST['date'])) {
            $date = $_POST['date'];
            if (isset($_POST['faculte'])) {
                $faculte = $_POST['faculte'];
                $allMessages = [];
                foreach ($faculte as $fac) {
                    // Vérifier si la faculté existe dans codif_delai
                    if (!faculteExisteDansDelai($connexion, $fac)) {
                        // Si elle n'existe pas → enregistrer l'étudiant
                        enregistrerEtudiant(
                            $connexion,
                            $num_etu,
                            $prenoms,
                            $nom,
                            $telephone,
                            $lieuNaissance,
                            $dateNaissance,
                            $fac,  // ici on utilise la faculté
                            $fac,
                            $fac,
                            $moyenne,
                            $numIdentite,
                            $sexe
                        );
                    }

                    // Toujours valider les dates (si tu veux garder ça)
                    $messages = validate_date_limite_codif_delai($fac, $nature, $date);

                    $allMessages = array_merge($allMessages, $messages);
                }

                // // Afficher les messages (succès ou erreur)
                // foreach ($allMessages as $message) {
                //     echo "<p>$message</p>";
                // }

                // Enregistrer les messages dans la session
                $_SESSION['all_messages'] = $allMessages;

                // Rediriger l'utilisateur vers le formulaire avec les messages
                header('Location: add_delai.php');
                exit;
            }
        }
    }
}

?>