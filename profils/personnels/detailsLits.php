<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
if (empty($_SESSION['classe'])) {
    header('location: /campuscoud.com/profils/personnels/niveau.php');
    exit();
}
include('../../traitement/fonction.php');
connexionBD();
include('../../traitement/requete.php');


if (isset($_POST['filter']) && $_POST['filter']) {
    $_SESSION['filter'] = $_POST['filter'];
}

if (isset($_SESSION['filter'])) {
    $resultatRequeteLitClasse = setFiltre_detail($_SESSION['filter'], $_SESSION['sexe_agent'], $_SESSION['classe']);
    $total_pagess = getPaginationFiltreClasse($_SESSION['classe'], $_SESSION['filter'], $_SESSION['sexe_agent']);
} else {
    $total_pagess = getLitByQuotas($_SESSION['classe'], $_SESSION['sexe_agent']);
}




$countIn = 0;
if (isset($_GET['erreurLitDeaffecter'])) {
    $_SESSION['erreurLitDeaffecter'] = $_GET['erreurLitDeaffecter'];
} else {
    $_SESSION['erreurLitDeaffecter'] = '';
}
if (isset($_GET['successLitDeaffecter'])) {
    $_SESSION['successLitDeaffecter'] = $_GET['successLitDeaffecter'];
} else {
    $_SESSION['successLitDeaffecter'] = '';
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <script src="../../assets/js/modernizr.js"></script>
    <script src="../../assets/js/pace.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php include('../../head.php'); ?>
    <div class="container">
        <div class="row">
            <div class="text-center">
                <h1>Quota lits : <?= $_SESSION['classe']; ?> : <?= $resultatRequeteLitClasse->num_rows ?> Lits</h1>
            </div>
            <div class="col-md-12" style="display:flex; justify-content: center;">
                <?php if ($_SESSION['erreurLitDeaffecter']) { ?>
                    <div class="col-md-6">
                        <div class="alert alert-danger" role="alert">
                            <?= $_SESSION['erreurLitDeaffecter']; ?>
                        </div>
                    </div>
                <?php } else if ($_SESSION['successLitDeaffecter']) { ?>
                    <div class="col-md-6">
                        <div class="alert alert-success" role="alert">
                            <?= $_SESSION['successLitDeaffecter']; ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <form class="d-flex" role="search" method="POST" action="../personnels/detailsLits" id="filterForm">
                    <select class="form-control me-2" placeholder="Search" aria-label="Search" name="filter" id="filter">
                        <option disabled selected>FILTRE PAVILLONS</option>
                        <?php
                        while ($rowPavillon = mysqli_fetch_array($resultatRequetePavillonClasse)) { ?>
                            <option value="<?= $rowPavillon['pavillon']; ?>"><?= $rowPavillon['pavillon']; ?></option>
                        <?php } ?>
                    </select>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form id="myForm" action="../../traitement/removeQuotas" method="GET">
                    <div class='options-container'>
                        <?php
                        while ($row = mysqli_fetch_array($resultatRequeteLitClasse)) {
                            if ($row['statut_migration'] == 'Migré dans les deux') {
                                $countIn++;
                            }
                            if ($counter % 9 == 0) { ?>
                                <div class='column'>
                                <?php
                            }
                            if ($row['statut_migration'] == 'Migré vers codif_quota uniquement') {
                                ?>
                                    <label class="option" title="Lit non choisi">
                                        <input type="checkbox" name="<?= $row['id_lit'] ?>" id="<?= $row['id_lit'] ?>"><?= $row['lit'] ?></input>
                                    </label>
                                <?php
                            }
                            if ($row['statut_migration'] == 'Migré dans les deux') {
                                ?>
                                    <label class="archive" title="Lit déja choisi!"><?= $row['lit'] ?> </label>
                                <?php
                            }
                            $counter++;
                            if ($counter % 9 == 0) { ?>
                                </div>
                            <?php
                            }
                        }
                        if ($counter % 9 != 0) { ?>
                    </div>
                <?php
                        } ?>
            </div><br>
            <div class="row justify-content-center">

                </style>
                <?php if ($countIn == 0) { ?>
                    <div class="col-md-2">
                        <button type='reset' onclick="choixs()" class="btn btn-outline-danger fw-bold btn-lg btn-block">EFFACER</button>
                    </div>
                <?php } else { ?> <style>
                        .option {
                            pointer-events: none;
                        }
                    </style> <?php } ?>
                <div class="col-md-2">
                    <select class='form-select' onchange='location = this.value;'>
                        <?php
                        for ($i = 1; $i <= $total_pagess; $i++) {
                            $offset_value = ($i - 1) * $limit;
                            $selected = ($i == $page) ? "selected" : "";
                            $lower_bound = $offset_value + 1;
                            $upper_bound = min($offset_value + $limit, $count_datas['total']);
                            echo "<option value='detailsLits.php?page=$i' $selected>De $lower_bound à $upper_bound</option>";
                        } ?>
                    </select>
                </div>
                <?php
                mysqli_close($connexion);
                ?>
                <?php if ($countIn == 0) { ?>
                    <div class="col-md-2">
                        <button class="btn btn-outline-success fw-bold bg-darkblue btn-lg btn-block" type='submit'>RETIRER</button>
                    </div>
                <?php } ?>
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