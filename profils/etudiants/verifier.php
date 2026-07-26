<?php

require '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: text/html; charset=utf-8');

if (!isset($_GET['token'])) {
    die("Aucun token reçu.");
}

$token = $_GET['token'];

try {

    // Lire la clé publique
    $publicKey = file_get_contents("../../private/public.pem");

    // Vérification du JWT
    $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

    echo "<pre>";
    print_r($decoded);
    echo "</pre>";

} catch (Exception $e) {

    echo "<h2 style='color:red'>JWT INVALIDE</h2>";

    echo $e->getMessage();
}