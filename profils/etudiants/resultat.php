<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}

require_once('../../traitement/fonction.php');

verif_type_mdp($_SESSION['username']);
ancien_eligible_2($_SESSION['username']);

if (isset($_GET['erreurNum_etu'])) {
    $_SESSION['erreurNum_etu'] = $_GET['erreurNum_etu'];
} else {
    $_SESSION['erreurNum_etu'] = '';
}

if (isset($_GET['data'])) {
    $tableau_data_etudiant = $_GET['data'];
} else {
    $num_etu = $_SESSION['num_etu'];
    $quota = getQuotaClasse($_SESSION['classe'], $_SESSION['sexe_etudiant'])['COUNT(*)'];
    $listeDelai1 = getAllDelai('choix', info($num_etu)[5]);
    $listeDelai2 = getAllDelai('validation', info($num_etu)[5]);
    $listeDelai3 = getAllDelai('paiement', info($num_etu)[5]);
   /* $date_limite_choix = dateFromat($listeDelai1['data_limite']);
    $date_limite_val = dateFromat($listeDelai2['data_limite']);
    $date_limite_paye = dateFromat($listeDelai3['data_limite']);*/
    $date_sys = dateFromat(date('Y-m-d'));
    $tableau_data_etudiant = getAllDatastudentStatus($quota, $_SESSION['classe'], $_SESSION['sexe_etudiant']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>

    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/base.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>

<body>
    <?php
    include('../../head.php'); ?>
    <div class="container">
        <?php
 
        if (isset($_SESSION['lit_choisi']) && $_SESSION['lit_choisi'] != '') {
        ?>
        <div class="alert alert-success" role="alert">
            Lit choisi: <?= $_SESSION['lit_choisi'] ?>
        </div>
        <!-- <a href="../convention/pdf">Télécharger convention</a> -->
        <?php } //else {
			if($_SESSION['sexe_etudiant']=='F'){$sexe='Filles';}
			if($_SESSION['sexe_etudiant']=='G'){$sexe='Garçons';}
            echo "<br><h2><b>Liste ".$_SESSION['classe']." / ".$sexe."</b></h2>";
        //}
        ?>
    </div>
    <div class="container-fluid mb-4">
        <table id="tableEtudiants" class="table table-hover table-bordered">
            <thead class="table-light text-center">
                <tr>
                    <th>N° Carte</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php

                function getRowClass($statut) {
                    switch ($statut) {
                        case 'Attributaire': return 'table-success';
                        case 'Forclos(e)': return 'table-dark';
                        case 'Suppleant(e)': return 'table-warning';
                        case 'Non Attributaire': return 'table-danger';
                        default: return 'table-secondary';
                    }
                }

                if (isset($_GET['data'])) {
                    $etu = $tableau_data_etudiant;
                    echo "<tr class='".getRowClass($etu['statut'])."'>
                            <td>{$etu['num_etu']}</td>
                            <td>{$etu['prenoms']}</td>
                            <td>{$etu['nom']}</td>
                            <td>{$etu['statut']}</td>
                          </tr>";
                } else {
                    foreach ($tableau_data_etudiant as $etu) {
                        echo "<tr class='".getRowClass($etu['statut'])."'>
                                <td>{$etu['num_etu']}</td>
                                <td>{$etu['prenoms']}</td>
                                <td>{$etu['nom']}</td>
                                <td>{$etu['statut']}</td>
                              </tr>";
                    }
                }
                ?>
            </tbody>
        </table>

    </div>
    <br><br>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#tableEtudiants').DataTable({
            pageLength: 10,
            order: [],
            ordering: true,
            searching: true,
            info: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            }
        });
    });
    </script>
    </div>

</body>

</html>