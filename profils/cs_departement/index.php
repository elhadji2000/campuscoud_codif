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


 //include('../../head.php'); ?>


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

    .col-5 {
        flex: none;
        /* Empêche l’expansion automatique */
    }
    </style>
</head>

<body>
    <div class="container-fluid" style="font-size:16px;">
    <br>
        <center>
            <div class="container" style="width:50%;">
                <form method="get" action="index">
                    <center>
                        <div class="row">
                            <div class="col-5">
                                <select class="pavillon" name="pavillon" required class="full-width">
                                    <option value="">Sélectionnez un pavillon</option>
                                    <?php
                                    // Boucle pour ajouter les options
                                    foreach ($pavillons as $pavillon) {
                                         echo "<option value='" . htmlspecialchars($pavillon) . "'>" . htmlspecialchars($pavillon) . "</option>";
                                     }?>
                                </select>
                            </div>
                            <div class="col-5">
                                <button type="submit" class="btn btn-primary pavillon">
                                    <strong>Rechercher</strong>
                                </button>
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
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Chambre</th>
                        <th scope="col">Lit</th>
                        <th scope="col">Num Etudiant</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Montant Facturé</th>
                        <th scope="col">Montant Payé</th>
                        <th scope="col">Restant</th>
                        <th scope="col">Rappel</th>
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
                        <td>
                            <button class="btn btn-secondary" disabled="disabled">rappel</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endforeach; ?>
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