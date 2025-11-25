<?php
session_start();
include('../../traitement/fonction.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    $destinataire = trim($_POST['destinataire']); // Id ou username du destinataire
    $expediteur = $_SESSION['campus']; // celui connecté

    if (!empty($message) && !empty($destinataire)) {
        $stmt = $connexion->prepare("INSERT INTO codif_messagerie (expediteur, destinataire, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $expediteur, $destinataire, $message);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "empty";
    }
}
?>

