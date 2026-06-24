<?php 
session_start();

include('../../traitement/fonction.php');

verif_type_mdp_2($_SESSION['username']);

$pavillons = getAllPavillons($connexion);

$pavillonDonne = isset($_GET["pavillon"])
    ? $_GET["pavillon"]
    : $pavillons[0];

$dateDebut = isset($_GET["date_debut"])
    ? $_GET["date_debut"]
    : '';

$dateFin = isset($_GET["date_fin"])
    ? $_GET["date_fin"]
    : '';

$result = getPaymentDetailsByPavillon1(
    $pavillonDonne,
    $connexion,
    $dateDebut,
    $dateFin
);

$totalFacture = 0;
$totalPaye = 0;
$totalRestant = 0;
$totalCaution = 0;
$totalLoyer = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<?php include('../../head.php'); ?>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestion Recettes</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <style>
    body {
        background: #f5f6fa;
        font-family: Arial, sans-serif;
    }

    .card {
        border: none;
        border-radius: 10px;
    }

    .table th,
    .table td {
        font-size: 12px!important;
        vertical-align: middle;
        white-space: nowrap;
    }
    .table th a{
        font-size: 12px !important;
    }

    .table thead th {
        background: #0d6efd;
        color: white;
    }

    .amount-cell {
        text-align: right;
        font-weight: bold;
    }

    .total-row {
        background: #f1f1f1;
        font-weight: bold;
    }

    .dt-buttons .btn {
        margin-right: 5px;
        margin-bottom: 5px;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 5px;
    }
    </style>

</head>

<body>

    <div class="container-fluid mt-3">

        <!-- FILTRE -->
        <div class="card mb-3">

            <div class="card-body">

                <form method="GET" class="row g-2">

                    <div class="col-md-4">

                        <select name="pavillon" class="form-select">

                            <?php foreach($pavillons as $pavillon): ?>

                            <option value="<?= $pavillon ?>" <?= ($pavillon == $pavillonDonne) ? 'selected' : '' ?>>

                                <?= $pavillon ?>

                            </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <input type="date" name="date_debut" class="form-control" value="<?= $dateDebut ?>">

                    </div>

                    <div class="col-md-3">

                        <input type="date" name="date_fin" class="form-control" value="<?= $dateFin ?>">

                    </div>

                    <div class="col-md-2 d-grid">

                        <button class="btn btn-primary">

                            <i class="fas fa-search"></i>
                            Filtrer

                        </button>

                    </div>

                </form>

            </div>

        </div>

        <!-- TABLE -->
        <div class="card">

            <div class="card-body">

                <div class="table-responsive">

                    <table id="tableRecette" class="table table-bordered table-hover w-100">

                        <thead>

                            <tr>

                                <th>#</th>
                                <th>Chambre</th>
                                <th>Lit</th>
                                <th>Numéro</th>
                                <th>Étudiant</th>
                                <th>Facturé</th>
                                <th>Payé</th>
                                <th>Caution</th>
                                <th>Loyer</th>
                                <th>Arrierer</th>
                                <th>Reste</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php if (!empty($result)): ?>

                            <?php $i = 1; ?>

                            <?php foreach ($result as $row): ?>

                            <?php
            // =========================
            // SAFE VALUES (anti NULL)
            // =========================

            $chambre = $row['chambre'] ?? '--';
            $lit = $row['lit'] ?? '--';
            $num_etu = $row['num_etu'] ?? '--';
            $etudiant_nom = trim(($row['etudiant_prenoms'] ?? '') . ' ' . ($row['etudiant_nom'] ?? ''));

            $facture = (int)($row['montant_facture_total'] ?? 0);
            $paye = (int)($row['montant_paye_total'] ?? 0);
            $caution = (int)($row['caution_payee'] ?? 0);
            $loyer = (int)($row['loyer_paye'] ?? 0);
            $resteAPayer = (int)($row['reste_a_payer_total'] ?? 0);
            $montant_arrierer = (int)($row['montant_arrierer'] ?? 0);

            // status
            $statusClass = ($resteAPayer == 0)
                ? 'status-paid'
                : (($resteAPayer >= 6000)
                    ? 'status-overdue'
                    : 'status-pending');

            // totals safe
            $totalFacture += $facture;
            $totalPaye += $paye;
            $totalRestant += $resteAPayer;
            $totalCaution += $caution;
            $totalLoyer += $loyer;
        ?>

                            <tr>

                                <td><?= $i++ ?></td>

                                <td><?= htmlspecialchars($chambre) ?></td>

                                <td><?= htmlspecialchars($lit) ?></td>

                                <td><?= htmlspecialchars($num_etu) ?></td>

                                <td><?= htmlspecialchars($etudiant_nom ?: '--') ?></td>

                                <td class="amount-cell">
                                    <?= number_format($facture, 0, ',', ' ') ?>
                                </td>

                                <td class="amount-cell">

                                    <a href="details.php?id_etu=<?= urlencode($row['etudiant_id'] ?? 0) ?>&etu=<?= urlencode($num_etu) ?>"
                                        class="text-decoration-underline" style="font-size:12px !important;">

                                        <span class="<?= $statusClass ?>"></span>

                                        <?= number_format($paye, 0, ',', ' ') ?>

                                    </a>

                                </td>

                                <td class="amount-cell">
                                    <?= number_format($caution, 0, ',', ' ') ?>
                                </td>

                                <td class="amount-cell">
                                    <?= number_format($loyer, 0, ',', ' ') ?>
                                </td>
                                 <td class="amount-cell">
                                    <?= number_format($montant_arrierer, 0, ',', ' ') ?>
                                </td>

                                <td class="amount-cell <?= ($resteAPayer > 0) ? 'text-danger' : 'text-success' ?>">
                                    <?= number_format($resteAPayer, 0, ',', ' ') ?>
                                </td>

                            </tr>

                            <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                        <tfoot>

                            <tr class="total-row">

                                <th colspan="5" class="text-end">
                                    TOTAUX
                                </th>

                                <th class="amount-cell">
                                    <?= number_format($totalFacture,0,',',' ') ?>
                                </th>

                                <th class="amount-cell">
                                    <?= number_format($totalPaye,0,',',' ') ?>
                                </th>

                                <th class="amount-cell">
                                    <?= number_format($totalCaution,0,',',' ') ?>
                                </th>

                                <th class="amount-cell">
                                    <?= number_format($totalLoyer,0,',',' ') ?>
                                </th>
                                <th class="amount-cell">
                                    <?= number_format($montant_arrierer,0,',',' ') ?>
                                </th>

                                <th class="amount-cell">
                                    <?= number_format($totalRestant,0,',',' ') ?>
                                </th>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <!-- Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>

    <!-- Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script>
    $(document).ready(function() {

        $('#tableRecette').DataTable({

            responsive: true,
            autoWidth: false,

            pageLength: 100,

            lengthMenu: [
                [100, 200, 300, 500, -1],
                [100, 200, 300, 500, "Tous"]
            ],

            dom: 'Blfrtip',

            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success'
            }],

            language: {
                search: "Recherche :",
                lengthMenu: "Afficher _MENU_ lignes",
                info: "_START_ à _END_ sur _TOTAL_ lignes",
                paginate: {
                    next: "Suivant",
                    previous: "Précédent"
                },
                zeroRecords: "Aucun résultat",
                infoEmpty: "Aucune donnée"
            }

        });

    });
    </script>

</body>

</html>