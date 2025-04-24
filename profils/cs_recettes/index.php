<?php session_start(); 

include('../../traitement/fonction.php');

verif_type_mdp_2($_SESSION['username']);
$pavillons = getAllPavillons($connexion);
$pavillonDonne = isset($_GET["pavillon"]) ? $_GET["pavillon"] : htmlspecialchars($pavillons[0]);
$result = getPaymentDetailsByPavillon($pavillonDonne, $connexion);

// Regrouper les lits par chambre
$chambres = [];
foreach ($result as $row) {
    $chambres[$row['chambre']][] = $row;
}

$totalFacture = 0;
$totalPaye = 0;
$totalRestant = 0;

 ?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>
    <!-- CSS================================================== -->
    <link rel="stylesheet" href="../../assets/css/main.css">
    <!-- script================================================== -->
    <script src="../../assets/js/modernizr.js"></script>
    <script src="../../assets/js/pace.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="../../assets/css/base.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <?php include('../../head.php'); ?>
    <style>
    select.pavillon {
        width: 250px;
        /* Ajuste la largeur */
        height: 50px;
        /* Augmente la hauteur */
        font-size: 16px;
        /* Augmente la taille du texte si nécessaire */
        padding: 5px;
        /* Ajoute un peu d’espace à l’intérieur */
        border-radius: 5px;
        /* Arrondi les bords */
    }

    .row {
        display: flex;
        align-items: center;
        /* Aligne les éléments verticalement */
        gap: 10px;
        /* Réduit l’espace entre les éléments */
    }

    </style>
</head>

<body>
    <div class="container-fluid" style="font-size:16px;">
        <br>
        <center>
            <div class="container" style="width:80%;">
                <form method="get" action="index">
                    <center>
                        <div class="row justify-content-center">
                            <!-- Select et bouton Rechercher collés -->
                            <div class="col-7 d-flex p-0">
                                <select class="pavillon form-control" name="pavillon" required>
                                    <option value="">Sélectionnez un pavillon</option>
                                    <?php
                                        // Boucle pour ajouter les options
                                        foreach ($pavillons as $pavillon) {
                                            echo "<option value='" . htmlspecialchars($pavillon) . "'>" . htmlspecialchars($pavillon) . "</option>";
                                        }
                                    ?>
                                </select>

                                <button type="submit" class="btn btn-primary pavillon ms-2">
                                    <strong>Rechercher</strong>
                                </button>
                            </div>

                            <!-- Bouton Download Excel aligné à droite -->
                            <div class="col-4 d-flex justify-content-end">
                                <a href="export_excel.php?pavillon=<?= urlencode($pavillonDonne) ?>"
                                    class="btn btn-info">
                                    <strong>Download Excel</strong>
                                </a>
                            </div>

                        </div>

                    </center>
                </form>
            </div>
        </center>
        <center>
            <br><br>
            <h1> GESTION DES RECOUVREMENTS</h1><br>
            <h2> PAVILLON : <?= htmlspecialchars($pavillonDonne) ?></h2>
        </center>
        <br><br>
        <center>
            <table class="table">
                <thead class="thead-dark">
                    <tr class="table-info">
                        <th scope="col"><strong>#</strong></th>
                        <th scope="col"><strong>Chambre</strong></th>
                        <th scope="col"><strong>Lit</strong></th>
                        <th scope="col"><strong>Num Etudiant</strong></th>
                        <th scope="col"><strong>Nom</strong></th>
                        <th scope="col"><strong>Montant Facturé</strong></th>
                        <th scope="col"><strong>Montant Payé</strong></th>
                        <th scope="col"><strong>Restant</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($chambres)) : ?>
                    <?php $counter = 1; ?>
                    <?php foreach ($chambres as $chambre => $lits) : ?>
                    <tr>
                        <th scope="row" rowspan="<?= count($lits) ?>"><?= $counter++ ?></th>
                        <td rowspan="<?= count($lits) ?>"><?= htmlspecialchars($chambre) ?></td>
                        <?php foreach ($lits as $i => $litRow) : ?>
                        <?php
                                    // Vérification du statut du rappel pour chaque étudiant dans la ligne
                                    $resteAPayer = (int)$litRow['reste_a_payer'];
                                    $canRemind = false;

                                    // Vérification du montant restant à payer et de la date du dernier rappel
                                    if ($resteAPayer >= 6000) {
                                        if (!empty($litRow['rappel_envoye'])) {
                                            $lastReminderDate = new DateTime($litRow['rappel_envoye']);
                                            $currentDate = new DateTime();
                                            $interval = $lastReminderDate->diff($currentDate);

                                            // Si le dernier rappel a plus de 2 mois, autoriser le rappel
                                            if ($interval->m >= 2) {
                                                $canRemind = true;
                                            }
                                        } else {
                                            $canRemind = true;  // Si aucun rappel n'a été envoyé
                                        }
                                    }
                                ?>
                        <?php if ($i > 0): ?>
                    <tr>
                        <?php endif; ?>
                        <td><?= htmlspecialchars($litRow['lit']) ?></td>
                        <td><?= htmlspecialchars($litRow['num_etu']) ?></td>
                        <td><?= htmlspecialchars($litRow['etudiant_prenoms'] . " " . $litRow['etudiant_nom']) ?></td>
                        <td><?= number_format($litRow['montant_facture'], 0, ',', ' ') ?> F CFA</td>
                        <td>
                            <a
                                href="details.php?id_etu=<?= urlencode($litRow['etudiant_id']) ?>&etu=<?= urlencode($litRow['num_etu']) ?>">
                                <?= number_format($litRow['montant_paye'], 0, ',', ' ') ?> F CFA
                            </a>
                        </td>
                        <td><?= number_format($litRow['reste_a_payer'], 0, ',', ' ') ?> F CFA</td>
                    </tr>
                    <?php 
                        $totalFacture += (int)$litRow['montant_facture'];
                        $totalPaye += (int)$litRow['montant_paye'];
                        $totalRestant += (int)$litRow['reste_a_payer'];                        
                    ?>

                    <?php endforeach; ?>
                    <?php endforeach; ?>
                    <tr class="table-warning">
                        <td colspan="5" class="text-center"><strong>TOTAUX :</strong></td>
                        <td><strong><?= number_format($totalFacture, 0, ',', ' ') ?> F CFA</strong></td>
                        <td><strong><?= number_format($totalPaye, 0, ',', ' ') ?> F CFA</strong></td>
                        <td><strong><?= number_format($totalRestant, 0, ',', ' ') ?> F CFA</strong></td>
                    </tr>
                    <?php else : ?>
                    <tr>
                        <td colspan="9">Aucun étudiant trouvé pour ce pavillon.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <br><br>
            <br><br>
            <button class="btn btn-success" onclick="goBack()">Retour</button>

            <script>
            function goBack() {
                window.history.back();
            }
            </script>
        </center>
    </div>
    <!-- footer
    ================================================== -->
    <footer>
        <div class="row">
            <div class="col-full">

                <div class="footer-logo">
                    <a class="footer-site-logo" href="#0"><img src="../../assets/images/logo.png" alt="Homepage"></a>
                </div>



            </div>
        </div>

        <div class="row footer-bottom">

            <div class="col-twelve">
                <div class="copyright">
                    <span>&copy;Copyright Centre des Oeuvres universitaires de Dakar</span>
                </div>

                <div class="go-top">
                    <a class="smoothscroll" title="Back to Top" href="#top"><i class="im im-arrow-up"
                            aria-hidden="true"></i></a>
                </div>
            </div>

        </div> <!-- end footer-bottom -->

    </footer> <!-- end footer -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <?php //include('footer.php'); ?>
    <script src="../../assets/js/script.js"></script>
    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>

</html>