<?php
session_start(); 
include('../../traitement/fonction.php');

verif_type_mdp_2($_SESSION['username']);
$campus = $_SESSION['campus'];
$etu = $_GET['etu'];
$id_etu = $_GET['id_etu'];
$info = info($etu);
$data = details($id_etu, $connexion) ;
$total= 0;
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
</head>
<?php include('../../head.php'); ?>

<body>
    <div class="container-fluid" style="font-size:16px;">
        <center>
            <br><br>
            <h1> details Paiement</h1><br>
            <h2> <?= htmlspecialchars($info[4]) ?> <?= htmlspecialchars($info[3]) ?></h2>
        </center>
        <br><br>
        <center>
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Quittance</th>
                        <th scope="col">Date Payement</th>
                        <th scope="col">Libelle</th>
                        <th scope="col">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (is_array($data) && !empty($data)) : ?>
                    <?php foreach ($data as $index => $row) : ?>

                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['id_paie']) ?></td>
                        <td><?= htmlspecialchars($row['dateTime_paie']) ?></td>
                        <td><?= htmlspecialchars($row['libelle']) ?></td>
                        <td><?= htmlspecialchars($row['montant']) ?></td>
                    </tr>
                    <?php $total += $row['montant']; // Calcul du total ?>
                    <?php endforeach; ?>
                    <tr style="font-weight: bold;">
                        <td colspan="3"></td>
                        <td>Total :</td>
                        <td><?= htmlspecialchars($total) ?></td>
                    </tr>
                    <?php else : ?>
                    <tr>
                        <td colspan="6">Aucun étudiant trouvé pour ce pavillon.</td>
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

    <script src="../../assets/js/script.js"></script>
    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>

</html>