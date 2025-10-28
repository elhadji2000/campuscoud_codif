<?php 
session_start(); 
include('../../traitement/fonction.php');

verif_type_mdp_2($_SESSION['username']);
$departements = getDepartements();

// On récupère le département sélectionné
$departementDonne = isset($_GET["departement"]) ? $_GET["departement"] : htmlspecialchars($departements[0]);

// On appelle la fonction adaptée
$result = getPaymentDetailsByDepartement($departementDonne, $connexion);

// Regrouper les lits par département (facultatif ici)
$etudiants = [];
foreach ($result as $row) {
    $etudiants[] = $row;
}
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
    select.departement {
        width: 250px;
        height: 50px;
        font-size: 16px;
        padding: 5px;
        border-radius: 5px;
    }

    .row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .col-5 {
        flex: none;
    }
    </style>
</head>

<body>
    <div class="container-fluid" style="font-size:16px;">
        <br>
        <center>
            <div class="container" style="width:50%;">
                <form method="get" action="black_list">
                    <div class="row">
                        <div class="col-5">
                            <select class="departement" name="departement" required>
                                <option value="">Sélectionnez un département</option>
                                <?php foreach ($departements as $dep) : ?>
                                <option value="<?= htmlspecialchars($dep) ?>"
                                    <?= ($dep == $departementDonne) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dep) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-5">
                            <button type="submit" class="btn btn-primary departement">
                                <strong>Rechercher</strong>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </center>

        <center>
            <h2>GESTION DES ARRIÈRÈS</h2>
            <p>DÉPARTEMENT : <?= htmlspecialchars($departementDonne) ?></p><br>
            <a href="black_list_view" class="text-decoration-underline"> voir les etudiants ajouter à la liste noir</a> |
            <a href="#" class="text-danger text-decoration-underline"
                onclick="ajouterBlackList('<?php echo $departementDonne; ?>')">
                Ajouter cette liste
            </a>

            <script>
            function ajouterBlackList(departement) {
                const annee = new Date().getFullYear(); // année en cours
                if (confirm(
                        `Voulez-vous vraiment ajouter les étudiants du département ${departement} à la black list ?`
                    )) {
                    window.location.href =
                        `black_list_trait.php?action=ajouter&departement=${encodeURIComponent(departement)}`;
                }
            }
            </script>

            <?php
                if (isset($_GET['msg'])) {
                    $message = urldecode($_GET['msg']);
                    echo "<br><br><div style='color:green; font-weight:bold;'>$message</div>";
                }
            ?>

        </center>

        <br><br>
        <center>
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Lit</th>
                        <th>Numéro Étudiant</th>
                        <th>Nom & Prénom</th>
                        <th>NiveauFormation</th>
                        <th>Telephone</th>
                        <th>Reste à Payer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($etudiants)) : ?>
                    <?php $counter = 1; ?>
                    <?php foreach ($etudiants as $etu) : ?>
                    <tr>
                        <th scope="row"><?= $counter++ ?></th>
                        <td><?= htmlspecialchars($etu['lit']) ?></td>
                        <td><?= htmlspecialchars($etu['num_etu'] ?? "") ?></td>
                        <td><?= htmlspecialchars($etu['etudiant_prenoms'] . " " . $etu['etudiant_nom']) ?></td>
                        <td><?= htmlspecialchars($etu['niveauFormation']) ?></td>
                        <td><?= htmlspecialchars($etu['telephone']) ?></td>
                        <td class="text-danger"><?= number_format($etu['reste_a_payer'], 0, ',', ' ') ?> f cfa</td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <tr>
                        <td colspan="8">Aucun étudiant trouvé pour ce département.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <br><br>
            <button class="btn btn-success" onclick="window.history.back()">Retour</button>
        </center>
    </div>

    <footer class="text-center mt-5">
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
                    <span>&copy; Copyright Centre des Œuvres Universitaires de Dakar</span>
                </div>
                <div class="go-top">
                    <a class="smoothscroll" title="Back to Top" href="#top"><i class="im im-arrow-up"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>

</html>