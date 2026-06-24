<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
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
    <div class="container">
        <div class="row">
            <form class="form" id="selectForm" action="" method="post">
                <div class="col-md-12">
                    <h4>Choisissez la Classe :</h4>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="selectFac">CHOISIR UNE FACULTE</label><span> *</span>
                        <select class="form-select" id="selectFac" aria-label="Default select example" onchange="populateData()" required="required">
                           
						   <option value="" selected>Faculté</option>
                            <?php 
							
                            while ($rowNiv = mysqli_fetch_array($dataEtablissement)) { ?>
                                <option value="<?= $rowNiv['etablissement']; ?>"><?= $rowNiv['etablissement']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="selectDep">CHOISIR UN DEPARTEMENT</label><span> *</span>
                        <select class="form-select" aria-label="Default select example" id="selectDep" required="required">
                            <option value="" selected>Département</option>
                            <?php
                            for ($i = 0; $i < count($tableauDataFaculte); $i++) { ?>
                                <option value="<?= $tableauDataFaculte[$i]; ?>"><?= $tableauDataFaculte[$i]; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="selectClasse">CHOISIR LA CLASSE</label><span> *</span>
                        <select class="form-select" aria-label="Default select example" required="required" id="selectClasse">
                            <option value="" selected>Classe</option>
                            <?php
                            for ($i = 0; $i < count($tableauDataNiveauFormation); $i++) { ?>
                                <option value="<?= $tableauDataNiveauFormation[$i]; ?>"><?= $tableauDataNiveauFormation[$i]; ?></option>
                            <?php
                            } ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>
<script src="../../assets/js/script.js"></script>

</html>