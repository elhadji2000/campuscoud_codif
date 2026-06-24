<?php
session_start();

if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud/');
    exit();
}

include ('../../traitement/fonction.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sécurisation des données reçues
    $num_etu = htmlspecialchars($_POST['num_etu']);
    $montant_total = (float) $_POST['montant_total'];
    $montant_encaisse = (float) $_POST['montant_encaisse'];
    $id_val = (float) $_POST['valide'];
    $id_etu = (float) $_POST['id_etu'];
    // var_dump($id_val);
    // exit;

    // Vérification de base
    if ($montant_encaisse <= 0 || $montant_encaisse > $montant_total) {
        header('Location: paiement?error=montant_invalide');
        exit();
    }

    try {
        // Démarrer une transaction (MySQLi)
        $connexion->begin_transaction();

        // 1️ Enregistrer le paiement dans la table `arrierer`
        $sql_insert = 'INSERT INTO arrierer (num_etu, montant_paye)
                       VALUES (?, ?)';
        $stmt = $connexion->prepare($sql_insert);
        $stmt->bind_param('sd', $num_etu, $montant_encaisse);
        $stmt->execute();

        $user = $_SESSION['username'];
        $chaine_libelle = 'ARRIERES';
        $datesys0 = date('Y-m-d');
        $datesys = strtotime($datesys0);
        $an0 = date('Y', $datesys);
        $accronyme = accronyme($user);  // echo $user;
        $link = connexionBD();
        $ins00 = "select max(num_ordre_user) as numauto from codif_paiement where an='$an0' and username_user='$user'";  // echo $ins00;
        $exx00 = mysqli_query($link, $ins00);
        $n_rows0 = mysqli_fetch_assoc($exx00);
        $ordre = $n_rows0['numauto'] + 1;
        $quittance = $an . '-' . $accronyme . '-' . $ordre;
        /* $requete = setPaiement($id_val, $user, $montant_encaisse, $chaine_libelle, $quittance, $an0, $ordre);
        if ($requete == 1) {
            $telephone = getTelephoneEtudiant($num_etu);
            // Envoi
            sms_paiement_etudiant($montant_encaisse, $num_etu, $quittance);
            // Stockage
            enreg_sms($num_etu, $telephone, 'paiement_chambre');
            // 2️ Calcul du nouveau montant restant
            $nouveau_montant = $montant_total - $montant_encaisse;
        } */

        $requete = setPaiement($id_val, $user, $montant_encaisse, $chaine_libelle, $quittance, $an0, $ordre);

        if ($requete == 1) {
            // récupérer le dernier ID inséré
            global $connexion;  // ou ta variable de connexion
            $id_insert = mysqli_insert_id($connexion);

            // mettre à jour id_etu avec cet ID
            $sql = "UPDATE codif_paiement SET id_etu = $id_etu WHERE id_paie = $id_insert";
            mysqli_query($connexion, $sql);

            $telephone = getTelephoneEtudiant($num_etu);

            sms_paiement_etudiant($montant_encaisse, $num_etu, $quittance);

            enreg_sms($num_etu, $telephone, 'paiement_chambre');

            $nouveau_montant = $montant_total - $montant_encaisse;
        }

        if ($nouveau_montant > 0) {
            // Mettre à jour le montant restant dans la blacklist
            $sql_update = 'UPDATE black_list SET reste_a_payer = ? WHERE num_etu = ?';
            $stmt2 = $connexion->prepare($sql_update);
            $stmt2->bind_param('ds', $nouveau_montant, $num_etu);
            $stmt2->execute();
        } else {
            // Supprimer si le reste à payer est 0
            $sql_delete = 'DELETE FROM black_list WHERE num_etu = ?';
            $stmt3 = $connexion->prepare($sql_delete);
            $stmt3->bind_param('s', $num_etu);
            $stmt3->execute();
        }

        // Valider la transaction
        $connexion->commit();

        // après traitement réussi
        header('Location: requestPaiement?numEtudiant=' . urlencode($num_etu));
        exit();

        exit();
    } catch (Exception $e) {
        $connexion->rollback();
        header('Location: paiement?error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location: paiement?error=methode_non_autorisee');
    exit();
}
?>