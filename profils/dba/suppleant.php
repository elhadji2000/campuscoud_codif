<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
require_once('../../traitement/fonction.php');

if (isset($_POST['filtre_sexe'])) {
    $data_formation = getAllNiveauFormation2($_POST['filtre_sexe']);
} else {
    $data_formation = getAllNiveauFormation2("C.E.R.E.R");
}
$dataEtablissement = getAllEtablissement();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION </title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php
    include('../../head.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <form class="d-flex" role="search" method="POST" action="" id="filterForm">
                    <select class="form-select" aria-label="Default select example" name="filtre_sexe">
                        <option value="" selected>Faculté</option>
                        <?php
                        while ($rowNiv = mysqli_fetch_array($dataEtablissement)) { ?>
                            <option value="<?= $rowNiv['etablissement']; ?>"><?= $rowNiv['etablissement']; ?></option>
                        <?php } ?>
                    </select>
                    <button type="submit" class="btn btn-success">FILTRER</button>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover ">
                <tr class="table-secondary" style="font-size: 16px; font-weight: 400;">
                    <td>N° Etudiant</td>
                    <td>PRENOM</td>
                    <td>NOM</td>
                    <td>FACULTE</td>
                    <td>NIVEAU FORMATION</td>
                    <td>SEXE</td>
                    <td>STATUT</td>
                </tr>
                <?php
                while ($row_formation = mysqli_fetch_array($data_formation)) {
                    $quota = getQuotaClasse($row_formation["niveauFormation"], $row_formation["sexe"])['COUNT(*)'];
                    $tableau_data_etudiant = getAllDatastudentStatus($quota, $row_formation["niveauFormation"], $row_formation["sexe"]);
                    for ($i = 0; $i < count($tableau_data_etudiant); $i++) {
                         // if ($tableau_data_etudiant[$i]['statut'] == 'Suppleant(e)') {  
                          if ($tableau_data_etudiant[$i]['statut'] == 'Suppleant(e)' and ($tableau_data_etudiant[$i]['niveauFormation'] == 'Dentaire / Licence 3')) { 
                          
                         // verifierDemarrage($tableau_data_etudiant[$i]['niveauFormation']);
                          
                          
                          ?>
                            <tr class="table-primary" style="font-size: 14px;">
                                <td><?= $tableau_data_etudiant[$i]['num_etu'] ?></td>
                                <td><?= $tableau_data_etudiant[$i]['prenoms'] ?></td>
                                <td><?= $tableau_data_etudiant[$i]['nom'] ?></td>
                                <td><?= $tableau_data_etudiant[$i]['etablissement'] ?></td>
                                <td><?= $tableau_data_etudiant[$i]['niveauFormation'] ?></td>
                                <td><?= $tableau_data_etudiant[$i]['sexe'] ?></td>
                                <td><?= $tableau_data_etudiant[$i]['statut'] ?></td>
                            </tr>
                <?php
                //Envoi SMS
             //  sms_suppleants($tableau_data_etudiant[$i]['num_etu']);   //die;
                            
                        }
                    }
                } ?>
            </table>
        </div>
    </div>
    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>

</html>