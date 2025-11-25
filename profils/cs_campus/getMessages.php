<?php
session_start();
include('../../traitement/fonction.php');

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    echo json_encode(["error" => "Utilisateur non connecté"]);
    exit;
}

$expediteur = $_SESSION['campus'];
$destinataire = isset($_GET['destinataire']) ? trim($_GET['destinataire']) : "";

if ($destinataire !== "") {
    $stmt = $connexion->prepare("
        SELECT id, expediteur, destinataire, message, date_envoi, lu
        FROM codif_messagerie
        WHERE (expediteur = ? AND destinataire = ?)
           OR (expediteur = ? AND destinataire = ?)
        ORDER BY date_envoi ASC
    ");

    if ($stmt) {
        $stmt->bind_param("ssss", $expediteur, $destinataire, $destinataire, $expediteur);
        $stmt->execute();
        $result = $stmt->get_result();

        $messages = [];
        $unreadCount = 0;

        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
            // Compter les messages non lus pour ce destinataire
            if ($row['destinataire'] === $expediteur && $row['lu'] == 0) {
                $unreadCount++;
            }
        }

        echo json_encode([
            'messages' => $messages,
            'unreadCount' => $unreadCount
        ]);
        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erreur lors de la préparation de la requête."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Aucun destinataire spécifié."]);
}

$connexion->close();
?>
