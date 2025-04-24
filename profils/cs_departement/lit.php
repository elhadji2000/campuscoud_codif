<?php session_start(); 

include('../../traitement/fonction.php');

verif_type_mdp_2($_SESSION['username']);
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id_paie'])  && isset($_GET['lit'])) {
    $paie = $_GET['id_paie'] ?? null;
    $lit = $_GET['lit'] ?? null;
}

$occupants = getEtudiantByLit($lit,$paie, $connexion); ;
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

    .col-5 {
        flex: none;
        /* Empêche l’expansion automatique */
    }
    </style>
</head>

<body>
    <div class="container-fluid" style="font-size:16px;">
        <br><br>
        <center>
            <h1> GESTION DES LOGEMENTS</h1>
            <br><br>
            <h2> Occupant du Lit <?= htmlspecialchars($lit) ?></h2>
            <br><br>
        </center>
        <center>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Num Étudiant</th>
                        <th>Nom</th>
                        <th>Prenom</th>
                        <th>Telephone</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($occupants)) : ?>
                    <?php foreach ($occupants as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['num_etu']) ?></td>
                        <td><?= htmlspecialchars($row['nom']) ?></td>
                        <td><?= htmlspecialchars($row['prenoms']) ?></td>
                        <td><?= htmlspecialchars($row['telephone']) ?></td>
                        <td><?= htmlspecialchars($row['statut_etudiant']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <tr>
                        <td colspan="5">Aucun étudiant trouvé pour ce pavillon.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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