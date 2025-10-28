<?php session_start(); 
include('../../traitement/fonction.php');
verif_type_mdp_2($_SESSION['username']);
$pavillons = getAllPavillons($connexion);
$pavillonDonne = isset($_GET["pavillon"]) ? $_GET["pavillon"] : htmlspecialchars($pavillons[0]);

// Récupération des dates de filtrage
$dateDebut = isset($_GET["date_debut"]) ? $_GET["date_debut"] : '';
$dateFin = isset($_GET["date_fin"]) ? $_GET["date_fin"] : '';

$result = getPaymentDetailsByPavillon1($pavillonDonne, $connexion, $dateDebut, $dateFin);
          

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
    <title>Campuscoud</title>

    <!-- CSS -->
     <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./index.css">

    <style>
    
    </style>

    <?php include('../../head.php'); ?>
</head>

<body>
    <div class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Gestion des Recettes</h1>
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
                    <!-- Sélection du pavillon -->
                    <div class="col-md-3">
                        <label for="pavillon" class="form-label">Pavillon</label>
                        <div class="input-group">
                            <select class="form-select pavillon-select" name="pavillon" id="pavillon" required>
                                <option value="">Sélectionnez un pavillon...</option>
                                <?php foreach ($pavillons as $pavillon): ?>
                                <option value="<?= htmlspecialchars($pavillon) ?>"
                                    <?= ($pavillon == $pavillonDonne) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pavillon) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Période de paiement -->
                    <div class="col-md-3">
                        <label for="date_debut" class="form-label">Date début</label>
                        <input type="date" id="date_debut" name="date_debut" class="form-control pavillon-select"
                            value="<?= htmlspecialchars($dateDebut) ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="date_fin" class="form-label">Date fin</label>
                        <input type="date" id="date_fin" name="date_fin" class="form-control pavillon-select"
                            value="<?= htmlspecialchars($dateFin) ?>">
                    </div>
                    <!-- Boutons d'action -->
                    <div class="col-md-3 d-flex align-items-end justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary btn-action">
                            <i class="fas fa-search me-1"></i> Appliquer
                        </button>
                        <a href="export_excel.php?pavillon=<?= urlencode($pavillonDonne) ?>&date_debut=<?= urlencode($dateDebut) ?>&date_fin=<?= urlencode($dateFin) ?>"
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
                            <tr style="font-family: 'Poppins', sans-serif !important;font-size:15px;">
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
                            <tr style="font-family: 'Poppins', sans-serif !important;font-size:15px;">
                                <?php if ($i == 0): ?>
                                <th scope="row" rowspan="<?= count($lits) ?>"><?= $counter++ ?></th>
                                <td rowspan="<?= count($lits) ?>">
                                    <span class="badge bg-primary"><?= htmlspecialchars($chambre) ?></span>
                                </td>
                                <?php endif; ?>
                                <td><?= htmlspecialchars($litRow['lit']) ?></td>
                                <td><?= htmlspecialchars($litRow['num_etu']?? "--") ?></td>
                                <td class="student-name">
                                    <?= htmlspecialchars($litRow['etudiant_prenoms'] . "--" . $litRow['etudiant_nom']) ?>
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
                                <td class="amount-cell">
                                    <?= number_format($litRow['caution_payee'], 0, ',', ' ') ?> F
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
                                            $totalCaution += (int)$litRow['caution_payee'];  
                                            $totalLoyer += (int)$litRow['loyer_paye'];
                                        ?>
                            <?php endforeach; ?>
                            <?php endforeach; ?>
                            <tr class="total-row" style="font-family: 'Poppins', sans-serif !important;font-size:15px;">
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
                            <tr style="font-family: 'Poppins', sans-serif !important;font-size:15px;">
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