<?php
session_start();

if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud/');
    exit();
}

include('../../traitement/fonction.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sécurisation des données reçues
    $num_etu = htmlspecialchars($_POST['num_etu']);
    $montant_total = (float) $_POST['montant_total'];
    $montant_encaisse = (float) $_POST['montant_encaisse'];

    // Vérification de base
    if ($montant_encaisse <= 0 || $montant_encaisse > $montant_total) {
        header("Location: paiement?error=montant_invalide");
        exit();
    }

    try {
        // Démarrer une transaction (MySQLi)
        $connexion->begin_transaction();

        // 1️⃣ Enregistrer le paiement dans la table `arrierer`
        $sql_insert = "INSERT INTO arrierer (num_etu, montant_paye)
                       VALUES (?, ?)";
        $stmt = $connexion->prepare($sql_insert);
        $stmt->bind_param("sd", $num_etu, $montant_encaisse);
        $stmt->execute();

        // 2️⃣ Calcul du nouveau montant restant
        $nouveau_montant = $montant_total - $montant_encaisse;

        if ($nouveau_montant > 0) {
            // Mettre à jour le montant restant dans la blacklist
            $sql_update = "UPDATE black_list SET reste_a_payer = ? WHERE num_etu = ?";
            $stmt2 = $connexion->prepare($sql_update);
            $stmt2->bind_param("ds", $nouveau_montant, $num_etu);
            $stmt2->execute();
        } else {
            // Supprimer si le reste à payer est 0
            $sql_delete = "DELETE FROM black_list WHERE num_etu = ?";
            $stmt3 = $connexion->prepare($sql_delete);
            $stmt3->bind_param("s", $num_etu);
            $stmt3->execute();
        }

        // Valider la transaction
        $connexion->commit();

        // après traitement réussi
        header("Location: requestPaiement?numEtudiant=" . urlencode($num_etu));
        exit();

        exit();

    } catch (Exception $e) {
        $connexion->rollback();
        header("Location: paiement?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: paiement?error=methode_non_autorisee");
    exit();
}
?>