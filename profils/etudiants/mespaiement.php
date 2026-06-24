<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
require('../../traitement/fonction.php');
$tableau_data_etudiant = getAllSituation($_SESSION['num_etu']);   
$id_etu=info($_SESSION['num_etu'])['15'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
 <?php
    include('../../head.php'); ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<div class="container mt-4">

    <?php
$montantLit = getPrixMensuelLit($_SESSION['num_etu']);
$indiv = (isIndivLitStudent($_SESSION['num_etu']) == "oui") ? 'Lit individuel' : 'Lit normal';

$rdate_debut = getAllDelai("depart", info($_SESSION['num_etu'])[5]);
$date_debut = dateFromat($rdate_debut['data_limite']);		
$nbr_mois_systeme_debut = calcul_nbreMois($date_debut);
$arr = getMontantArrierer($connexion, $_SESSION['num_etu']);

if(verifCaution($id_etu)){
    $totalfacture = ($montantLit * $nbr_mois_systeme_debut) + 5000 + $arr;
} else {
    $totalfacture = ($montantLit * $nbr_mois_systeme_debut + $arr);
}



$totalpaye = getTotalPaye($_SESSION['num_etu']) ?? 0;
$restant = $totalfacture - $totalpaye;
?>

    <div class="row g-4">

        <!-- INFOS FACTURATION -->
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    Infos de facturation
                </div>
                <div class="card-body">
                    <p><b>Type de lit :</b> <?= $indiv; ?>; <b>Caution :</b> 5000 F;
                        <b>Mensualité :</b> <?= $montantLit; ?> F; <b>Mois facturés :</b>
                        <?= $nbr_mois_systeme_debut; ?>
                    </p>
                    <hr>
                    <p><b>Total facturé :</b> <?= $totalfacture; ?> F ; <b>Total payé :</b>
                        <?= $totalpaye; ?> F</p>

                    <?php if($restant >= 0): ?>
                    <p class="text-danger"><b>À payer :</b> <?= $restant; ?> F</p>
                    <?php else: ?>
                    <p class="text-success"><b>Avance :</b> <?= abs($restant); ?> F</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- HISTORIQUE -->
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    Historique des paiements
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="tablePaiement" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Quittance</th>
                                    <th>Date</th>
                                    <th>Libellé</th>
                                    <th>Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_array($tableau_data_etudiant)): ?>
                                <tr>
                                    <td><?= $row['quittance'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['dateTime_paie'])) ?></td>
                                    <td><?= $row['libelle'] ?></td>
                                    <td><?= $row['montant'] ?> F</td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tablePaiement').DataTable({
        pageLength: 10,
        order: [],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
        }
    });
});
</script>
<style>
#tablePaiement th,
#tablePaiement td {
    vertical-align: middle;
    font-size: 12px;
}
</style>
</body>

</html>