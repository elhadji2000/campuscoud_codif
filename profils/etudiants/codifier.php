<?php session_start();

if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}



require('../../traitement/fonction.php');
verif_type_mdp($_SESSION['username']);

//$total_pagesEtudiant = getLitByStudent($_SESSION['classe'], $_SESSION['sexe_etudiant']);
$resultRequeteLitClasseEtudiant = getLitValideByClasse($_SESSION['classe'], $_SESSION['sexe_etudiant']);
if (isset($_GET['erreurLitCodifier'])) {
    $_SESSION['erreurLitCodifier'] = $_GET['erreurLitCodifier'];
} else {
    $_SESSION['erreurLitCodifier'] = '';
}

if (isset($_POST['filter']) && $_POST['filter']) {
    $_SESSION['filter'] = $_POST['filter'];
}

//echo "hello ".$_SESSION['classe'];
verifierDemarrage($_SESSION['classe']);


//if (isset($_SESSION['filter'])) {
    // $_SESSION['filter'] = isset($_POST['filter']) ? $_POST['filter'] : '';
   // $resultRequeteLitClasseEtudiant = setFiltre($_SESSION['filter'], $_SESSION['sexe_etudiant']);
   // $total_pagesEtudiant = getPaginationFiltreClasse($_SESSION['filter'], $_SESSION['sexe_etudiant']);
//} else {
   // $total_pagesEtudiant = getLitByQuotas($_SESSION['classe'], $_SESSION['sexe_etudiant']);
    $total_lits=getLitOneByNiveau($_SESSION['classe'], $_SESSION['sexe_etudiant']);  //var_dump($total_lits); die;
//}

$resultatRequetePavillonClasse = getPavillonOneByNiveau($_SESSION['classe'], $_SESSION['sexe_etudiant']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION </title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php include('../../head.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>VEUILLEZ CHOISIR UN LIT !!!</h1>
            </div>
            <div class="col-md-12" style="display:flex; justify-content: center;">
                <?php if ($_SESSION['erreurLitCodifier']) { ?>
                    <div class="col-md-3">
                        <div class="alert alert-danger" role="alert">
                            <?= $_SESSION['erreurLitCodifier']; ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <!--div class="row">
            <div class="col-md-2">
                <form class="d-flex" role="search" method="POST" action="codifier" id="filterForm">
                    <select class="form-control me-2" placeholder="Search" aria-label="Search" name="filter" id="filter">
                        <option disabled selected>FILTRE PAVILLONS</option>
                        <?php/*
                        while ($rowPavillon = mysqli_fetch_array($resultatRequetePavillonClasse)) { ?>
                            <option value="<?= $rowPavillon['pavillon']; ?>"><?= $rowPavillon['pavillon']; ?></option>
                        <?php*/  ?>
                    </select>
                </form>
            </div>
        </div-->
        <div class="row">
            <div class="col-md-12">
                <ul class="options">
                    <form id="myForm" action="traitementCodifier" method="GET">
                        <div class='options-container'>
                            <?php
                            while ($row = mysqli_fetch_array($total_lits)) {
                                if ($counter % 10 == 0) { ?>
                                    <div class='column'>
                                    <?php
                                }
                                if ($row['statut_migration'] == 'Migré vers codif_quota uniquement') {
                                    ?>
                                        <label class="optionEtu" title="Lit non choisi !">
                                            <input type="radio" name="lit_selection" value="<?= $row['id_lit'] ?>"><?= $row['lit'] ?></input>
                                        </label>
                                    <?php
                                }
                                if ($row['statut_migration'] == 'Migré dans les deux') {
                                    ?>
                                        <label class="archive" title="Lit affecté"><?= $row['lit'] ?> </label>
                                    <?php
                                }
                                $counter++;
                                if ($counter % 10 == 0) { ?>
                                    </div>
                                <?php
                                }
                            }
                            if ($counter % 10 != 0) { ?>
                        </div>

                    <?php
                            } ?>
            </div><br><br>
            <div class="row justify-content-center">
                <div class="col-md-2">
                    <input type='reset' onclick="choi()" class="btn btn-outline-danger fw-bold" title="Annulé la selectionnée" value="REINITIALISER">
                </div>
                <!--div class="col-md-2">
                    <select class='form-select' onchange='location = this.value;'>
                        <?php/*
                        for ($i = 1; $i <= $total_pagesEtudiant; $i++) {
                            $offset_value = ($i - 1) * $limit;
                            $selected = ($i == $page) ? "selected" : "";
                            $lower_bound = $offset_value + 1;
                            $upper_bound = min($offset_value + $limit, $count_datas['total']);
                            echo "<option value='codifier.php?page=$i' $selected>De $lower_bound à $upper_bound</option>";
                        } -*?>
                    </select>
                </div-->
                <?php
                mysqli_close($connexion);
                ?>
                <div class="col-md-2">
                    <button class="btn btn-outline-success fw-bold" type="submit" title="Sauvegarder les lits selectionnés">VALIDER</button>
                </div>
            </div>
            </form>
            </ul>
        </div>
    </div>
</body>

<script>
    function choi() {
        window.location.href = "codifier";
    }
</script>
<script src="../../assets/js/script.js"></script>

</html>