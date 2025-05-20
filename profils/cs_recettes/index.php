<?php session_start(); 
include('../../traitement/fonction.php');
verif_type_mdp_2($_SESSION['username']);
$pavillons = getAllPavillons($connexion);
$pavillonDonne = isset($_GET["pavillon"]) ? $_GET["pavillon"] : htmlspecialchars($pavillons[0]);
$result = getPaymentDetailsByPavillon($pavillonDonne, $connexion);

// Regrouper les lits par chambre
$chambres = [];
foreach ($result as $row) {
    $chambres[$row['chambre']][] = $row;
}

$totalFacture = 0;
$totalPaye = 0;
$totalRestant = 0;
$totalCaution = 0;
$totalLoyer = 0;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD - Gestion des Recouvrements</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
    :root {
        --primary-color: #3498db;
        --secondary-color:rgb(26, 102, 177);
        --success-color: #27ae60;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f5f7fa;
        color: #333;
    }

    .header {
        background-color: var(--light-color);
        color: black;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    .card-header {
        background-color: var(--secondary-color);
        color: white;
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
        font-weight: 600;
    }

    .table-container {
        overflow-x: auto;
    }

    .table {
        border-radius: 10px;
        overflow: hidden;
    }

    .table thead th {
        background-color: var(--secondary-color);
        color: white;
        font-weight: 500;
        vertical-align: middle;
        text-align: center;
    }
    .table tbody td {
        text-align: center;
    }
    .table tbody th {
        text-align: center;
    }

    .table tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.05);
    }

    .badge-pill {
        border-radius: 50px;
        padding: 5px 10px;
        font-weight: 500;
    }

    .btn-action {
        border-radius: 50px;
        padding: 8px 15px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-action:hover {
        transform: translateY(-2px);
    }

    .total-row {
        font-weight: 600;
        background-color: rgba(249, 202, 36, 0.2) !important;
    }

    .pavillon-select {
        border-radius: 50px;
        padding: 10px 20px;
        height: auto;
        border: 2px solid var(--primary-color);
    }

    .status-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 5px;
    }

    .status-paid {
        background-color: var(--success-color);
    }

    .status-pending {
        background-color: var(--warning-color);
    }

    .status-overdue {
        background-color: var(--danger-color);
    }

    .amount-cell {
        font-weight: 500;
        text-align: right !important;
    }

    .student-name {
        font-weight: 500;
        color: var(--secondary-color);
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 14px;
        }

        .btn-action {
            padding: 5px 10px;
            font-size: 14px;
        }
    }
    </style>

    <?php include('../../head.php'); ?>
</head>

<body>
    <div class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Gestion des Recouvrements</h1>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-building me-1"></i> <?= htmlspecialchars($pavillonDonne) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card mb-4">
            <div class="card-body">
                <form method="get" action="index" class="row g-3 align-items-center">
                    <div class="col-md-8">
                        <div class="input-group">
                            <select class="form-select pavillon-select" name="pavillon" required>
                                <option value="">Sélectionnez un pavillon...</option>
                                <?php foreach ($pavillons as $pavillon): ?>
                                <option value="<?= htmlspecialchars($pavillon) ?>"
                                    <?= ($pavillon == $pavillonDonne) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pavillon) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary btn-action ms-2">
                                <i class="fas fa-search me-1"></i> Rechercher
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="export_excel.php?pavillon=<?= urlencode($pavillonDonne) ?>"
                            class="btn btn-success btn-action">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list me-2"></i>Détails des recouvrements</span>
                    <span class="badge bg-light text-dark">
                        <?= count($result) ?> <?= (count($result) > 1) ? 'étudiants' : 'étudiant' ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="10%">Chambre</th>
                                <th width="8%">Lit</th>
                                <th width="12%">Numéro</th>
                                <th width="15%">Étudiant</th>
                                <th width="10%" class="text-end">Facturé</th>
                                <th width="10%" class="text-end">Payé</th>
                                <th width="10%" class="text-end">Caution</th>
                                <th width="10%" class="text-end">Loyer</th>
                                <th width="10%" class="text-end">Reste</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($chambres)): ?>
                            <?php $counter = 1; ?>
                            <?php foreach ($chambres as $chambre => $lits): ?>
                            <?php foreach ($lits as $i => $litRow): ?>
                            <?php
                                            $resteAPayer = (int)$litRow['reste_a_payer_total'];
                                            $statusClass = ($resteAPayer == 0) ? 'status-paid' : 
                                                         (($resteAPayer >= 6000) ? 'status-overdue' : 'status-pending');
                                        ?>
                            <tr>
                                <?php if ($i == 0): ?>
                                <th scope="row" rowspan="<?= count($lits) ?>"><?= $counter++ ?></th>
                                <td rowspan="<?= count($lits) ?>">
                                    <span class="badge bg-primary"><?= htmlspecialchars($chambre) ?></span>
                                </td>
                                <?php endif; ?>
                                <td><?= htmlspecialchars($litRow['lit']) ?></td>
                                <td><?= htmlspecialchars($litRow['num_etu']) ?></td>
                                <td class="student-name">
                                    <?= htmlspecialchars($litRow['etudiant_prenoms'] . " " . $litRow['etudiant_nom']) ?>
                                </td>
                                <td class="amount-cell">
                                    <?= number_format($litRow['montant_facture_total'], 0, ',', ' ') ?> F</td>
                                <td class="amount-cell">
                                    <a href="details.php?id_etu=<?= urlencode($litRow['etudiant_id']) ?>&etu=<?= urlencode($litRow['num_etu']) ?>"
                                        class="text-decoration-none">
                                        <span class="<?= $statusClass ?>"></span>
                                        <?= number_format($litRow['montant_paye_total'], 0, ',', ' ') ?> F
                                    </a>
                                </td>
                                <td class="amount-cell"><?= number_format($litRow['montant_caution_facture'], 0, ',', ' ') ?> F
                                </td>
                                <td class="amount-cell"><?= number_format($litRow['loyer_paye'], 0, ',', ' ') ?> F</td>
                                <td
                                    class="amount-cell <?= ($resteAPayer > 0) ? 'text-danger fw-bold' : 'text-success' ?>">
                                    <?= number_format($resteAPayer, 0, ',', ' ') ?> F
                                </td>
                            </tr>
                            <?php 
                                            $totalFacture += (int)$litRow['montant_facture_total'];
                                            $totalPaye += (int)$litRow['montant_paye_total'];
                                            $totalRestant += (int)$litRow['reste_a_payer_total']; 
                                            $totalCaution += (int)$litRow['montant_caution_facture'];  
                                            $totalLoyer += (int)$litRow['loyer_paye'];
                                        ?>
                            <?php endforeach; ?>
                            <?php endforeach; ?>
                            <tr class="total-row">
                                <td colspan="5" class="text-end fw-bold">TOTAUX :</td>
                                <td class="amount-cell"><?= number_format($totalFacture, 0, ',', ' ') ?> F</td>
                                <td class="amount-cell"><?= number_format($totalPaye, 0, ',', ' ') ?> F</td>
                                <td class="amount-cell"><?= number_format($totalCaution, 0, ',', ' ') ?> F</td>
                                <td class="amount-cell"><?= number_format($totalLoyer, 0, ',', ' ') ?> F</td>
                                <td class="amount-cell <?= ($totalRestant > 0) ? 'text-danger' : 'text-success' ?>">
                                    <?= number_format($totalRestant, 0, ',', ' ') ?> F
                                </td>
                            </tr>
                            <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i> Aucun étudiant trouvé pour ce pavillon
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <button class="btn btn-secondary btn-action" onclick="goBack()">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </button>
        </div>
    </div>

    <script>
    function goBack() {
        window.history.back();
    }
    </script>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/script.js"></script>
</body>

</html>