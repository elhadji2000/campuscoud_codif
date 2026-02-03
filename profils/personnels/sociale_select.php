<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud/');
    exit();
} 
if (isset($_GET['classe'])) {
    $_SESSION['classe'] = $_GET['classe']."/SOCIALE";
    $_SESSION['fac2'] = $_GET['classe'];  // Enregistre la classe dans la session
    header("Location: listeLits"); // Redirige vers la page cible
    exit();
}
unset($_SESSION['classe']);
include('../../traitement/fonction.php');
include('../../traitement/requete.php');

verif_type_mdp_2($_SESSION['username']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="log.gif" type="image/x-icon">
    <link rel="icon" href="log.gif" type="image/x-icon">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.bundle.min.js">
</head>

<body>
    <?php include('../../head.php'); ?>
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <form class="form" id="selectForm" action="" method="get" style="text-align:center;">
                <div class="col-md-12 mb-3">
                    <h4>Choisissez la Faculté :</h4>
                </div>

                <div class="row justify-content-center align-items-end">
                    <div class="col-md-4 mb-3">
                        <label for="selectFac">CHOISIR UNE FACULTÉ</label><span> *</span>
                        <select class="form-select" id="selectFac" name="classe" aria-label="Default select example" required>
                            <option value="" selected>Faculté</option>
                            <?php 
                        while ($rowNiv = mysqli_fetch_array($dataEtablissement)) { ?>
                            <option value="<?= $rowNiv['etablissement']; ?>"><?= $rowNiv['etablissement']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-auto mb-3" style="margin-top:32px;">
                        <button type="submit" style="padding:11px 10px;" class="btn btn-primary">SUIVANT</button>
                    </div>
                </div>
            </form>
        </div>
        </br></br>
        <div class="row justify-content-center text-decoration-underline"> <a href="index">Menu</a></div>
    </div>
    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>

</html>