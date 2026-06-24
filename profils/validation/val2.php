<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}

include ('../../traitement/fonction.php');
connexionBD();
include ('../../traitement/requete.php');

verif_type_mdp_2($_SESSION['username']);

$countIn = 0;
$messages = [
    'erreurValider' => $_GET['erreurValider'] ?? null,
    'successValider' => $_GET['successValider'] ?? null,
    'erreurNonTrouver' => $_GET['erreurNonTrouver'] ?? null,
    'erreurForclo' => $_GET['erreurForclo'] ?? null,
];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php include ('../../head.php'); ?>

    <div class="container">
        <div class="row">
            <div class="text-center">
                <h1>VALIDATION PAR PRESENCE PHYSIQUE</h1><br>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">

                <?php foreach ($messages as $key => $msg): ?>
                <?php if ($msg): ?>
                <div class="alert 
                    <?= str_contains($key, 'success') ? 'alert-success' : 'alert-danger' ?>">
                    <?= htmlspecialchars($msg) ?>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>

            </div>
        </div>
        <div class="container" style="width:50%;">
            <form action="requestValidation" method="POST">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-6">

                        <input id="numEtudiant" name="numEtudiant" type="text" class="form-control text-uppercase"
                            placeholder="NUMERO CARTE ETUDIANT" required>
                    </div>
                    <div class="col-md-6">

                        <button type="submit" width="100px" class="btn btn-lg btn-primary">
                            <i class="fa fa-search"></i> Rechercher
                        </button>

                    </div>
                </div>
            </form>
        </div>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">

                    <?php
                if (isset($_GET['data'])) {
                    $data = $_GET['data'];

                    $tableau_data_etudiant = getAllSituation($data['num_etu']);

                    if ($data['departement'] == 'F.L.S.H/M1ASS') {
                        echo "<div class='alert alert-warning text-center'>
                                Codification momentanément suspendue pour votre formation !
                            </div>";
                        exit();
                    }

                    $rowClass = 'row justify-content-center text-dark';
                    ?>

                    <?php if (isset($_GET['statut'])): ?>

                    <?php endif;?>
                    <?php } ?>
                </div><br>




                <script>
                document.getElementById('numEtudiant').addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
                </script>
                <script src="../../assets/js/jquery-3.2.1.min.js"></script>
                <script src="../../assets/js/plugins.js"></script>
                <script src="../../assets/js/main.js"></script>

                <!-- JavaScript de Bootstrap (assurez-vous d'ajuster le chemin si nécessaire) -->
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
<script src="../../assets/js/script.js"></script>
</body>

</html>