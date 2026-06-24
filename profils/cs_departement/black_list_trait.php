<?php
session_start();
include ('../../traitement/fonction.php');

if (isset($_GET['action']) && $_GET['action'] === 'ajouter' && isset($_GET['faculter'])) {
    $faculter = $_GET['faculter'];
    $annee = date('Y');

    // Récupérer la liste des étudiants du département
    $result = getPaymentDetailsByFaculter($faculter, $connexion);

    // Préparer la requête d'insertion
    $stmt = $connexion->prepare('
        INSERT INTO black_list (num_etu, nom, prenom, lit, departement, faculte, reste_a_payer, telephone, annee)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    if (!$stmt) {
        die('Erreur préparation : ' . $connexion->error);
    }

    $compteur = 0;

    foreach ($result as $row) {
        $stmt->bind_param(
            'ssssssdss',
            $row['num_etu'],
            $row['etudiant_nom'],
            $row['etudiant_prenoms'],
            $row['lit'],
            $row['departement'],
            $faculter,
            $row['reste_a_payer'],
            $row['telephone'],
            $annee
        );
        $stmt->execute();
        $compteur++;
    }

    //  Créer un message à passer dans l’URL
    $message = urlencode(" $compteur étudiants ont été ajoutés dans la black_list pour le faculté $faculter (année $annee).");

    //  Redirection avec un seul paramètre
    header("Location: ../../profils/cs_departement/black_list?msg=$message");
    exit();
}
?>
