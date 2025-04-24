<?php session_start();  
include('../../traitement/fonction.php');
$pavillon = "B1";
$id_etu = $_GET['id_etu'];
$etu = $_GET['etu'];

$info = info($etu);
$data = details($id_etu, $connexion);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<?php include('../../head.php'); ?>
</head>

<body>

    <div class="container-fluid">
        <center>
            <h2> Details du Paiement</h2><br> 
            <h1> <?= htmlspecialchars($info[4]) ?> <?= htmlspecialchars($info[3]) ?></h1>
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
                    <?php if (!empty($data)) : ?>
                    <?php foreach ($data as $index => $row) : ?>

                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['quittance']) ?></td>
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
                        <td colspan="6">Aucune info trouvée !</td>
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
    <?php //include('footer.php'); ?>

    <script src="assets/js/script.js"></script>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>