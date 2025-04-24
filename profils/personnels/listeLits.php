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

$faculte=getFaculteByNiveauFormation($_SESSION['classe']);

if (isset($_POST['filter']) && $_POST['filter']) {
    $_SESSION['filter'] = $_POST['filter'];
}

if (isset($_SESSION['filter'])) {
    $resultatRequeteTotalLit = setFiltre($_SESSION['filter'], $_SESSION['sexe_agent']);
    $total_lit_pages = getPaginationFiltre($_SESSION['filter'], $_SESSION['sexe_agent']);
} else {
    $total_lit_pages = getAllLitPagination($_SESSION['sexe_agent']);
}

if (isset($_GET['erreurLitAffecter'])) {
    $_SESSION['erreurLitAffecter'] = $_GET['erreurLitAffecter'];
} else {
    $_SESSION['erreurLitAffecter'] = '';
}
if (isset($_GET['successLitAffecter'])) {
    $_SESSION['successLitAffecter'] = $_GET['successLitAffecter'];
} else {
    $_SESSION['successLitAffecter'] = '';
}

controlSaisieQuota($faculte);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <script src="../assets/js/modernizr.js"></script>
    <script src="../assets/js/pace.min.js"></script>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php include('../../head.php'); 
    
//Eviter ajout quota apres debut choix lits
if (verifajoutquota($_SESSION['classe'])) {
    echo "<script>alert('les etudiant de cette classe ont déjà commencé a choisir des lits. Vous ne pouvez plus ajouter de lits au quota.');</script>";
    exit ();
} 
    
    ?>
    <div class="container-fluid">
    </div>
    <div class="container">
        <div class="text-center">
            <h1>Alouer un quota à la : <?= $_SESSION['classe']; ?> </h1>
        </div>
        <div class="row">
            <div class="col-md-12" style="display:flex; justify-content: center;">
                <?php if ($_SESSION['erreurLitAffecter']) { ?>
                    <div class="col-md-6">
                        <div class="alert alert-danger" role="alert">
                            <?= $_SESSION['erreurLitAffecter']; ?>
                        </div>
                    </div>
                <?php } else if ($_SESSION['successLitAffecter']) { ?>
                    <div class="col-md-6">
                        <div class="alert alert-success" role="alert">
                            <?= $_SESSION['successLitAffecter']; ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <form class="d-flex" role="search" method="POST" action="../personnels/listeLits" id="filterForm">
                    <select class="form-control me-2" placeholder="Search" aria-label="Search" name="filter" id="filter">
                        <option disabled selected>FILTRE PAVILLONS</option>
                        <?php
                        while ($rowPavillon = mysqli_fetch_array($resultatRequetePavillon)) { ?>
                            <option value="<?= $rowPavillon['pavillon']; ?>"><?= $rowPavillon['pavillon']; ?></option>
                        <?php } ?>
                    </select>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form id="myForm" action="../../traitement/addQuotas" method="GET">
                    <div class='options-container'>
                        <?php
                        while ($row = mysqli_fetch_array($resultatRequeteTotalLit)) {
                            if ($counter % 9 == 0) { ?>
                                <div class='column'>
                                <?php
                            }
                            if ($row['statut_migration'] == 'Non migré') { ?>
                                    <label class="option" title="Lit non affecté">
                                        <input type="checkbox" name="<?= $row['id_lit'] ?>" id="<?= $row['id_lit'] ?>"><?= $row['lit'] ?></input>
                                    </label>
                                <?php
                            } else { ?>
                                    <label class="archive" title="Lit affecté"><?= $row['lit'] ?> </label>
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
                <div class="col-md-2">
                    <button type='reset' onclick="choix()" class="btn btn-outline-danger fw-bold btn-lg btn-block" title="Annulé la selection">EFFACER</button>
                </div>
                <!--div class="col-md-2">
                    <select class='form-select' onchange='location = this.value;'>
                        <?php/*
                        for ($i = 1; $i <= $total_lit_pages; $i++) {
                            $offset_value = ($i - 1) * $limit;
                            $selected = ($i == $page) ? "selected" : "";
                            $lower_bound = $offset_value + 1;
                            $upper_bound = min($offset_value + $limit, $count_data_total['total']);
                            echo "<option value='listeLits.php?page=$i' $selected>De $lower_bound à $upper_bound</option>";
                        } */?>
                    </select>
                </div-->
                <?php
                mysqli_close($connexion);
                ?>
                <div class="col-md-2">
                    <button class="btn btn-outline-success fw-bold bg-darkblue btn-lg btn-block" type='submit' title="Sauvegarder les lits selectionnées">ENREGISTRER</button>
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