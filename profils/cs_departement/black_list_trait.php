<?php
session_start(); 
include('../../traitement/fonction.php');

if (isset($_GET['action']) && $_GET['action'] === 'ajouter' && isset($_GET['departement'])) {
    $departement = $_GET['departement'];
    $annee = date("Y");

    // Récupérer la liste des étudiants du département
    $result = getPaymentDetailsByDepartement($departement, $connexion);

    // Préparer la requête d'insertion
    $stmt = $connexion->prepare("
        INSERT INTO black_list (num_etu, nom, prenom, lit, departement, reste_a_payer, telephone, annee)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Erreur préparation : " . $connexion->error);
    }

    $compteur = 0;

    foreach ($result as $row) {
        $stmt->bind_param(
            "ssssssss",
            $row['num_etu'],
            $row['etudiant_nom'],
            $row['etudiant_prenoms'],
            $row['lit'],
            $row['departement'],
            $row['reste_a_payer'],
            $row['telephone'],
            $annee
        );
        $stmt->execute();
        $compteur++;
    }

    // ✅ Créer un message à passer dans l’URL
    $message = urlencode("✅ $compteur étudiants ont été ajoutés dans la black_list pour le département $departement (année $annee).");

    // ✅ Redirection avec un seul paramètre
    header("Location: ../../profils/cs_departement/black_list?msg=$message");
    exit();
}
?>
