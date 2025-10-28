<?php session_start(); 
include('../../traitement/fonction.php');
verif_type_mdp_2($_SESSION['username']);
$campus = getAllCampus($connexion);
$campusDonnee = isset($_GET["campus"]) ? $_GET["campus"] : "global";

// Récupération des dates de filtrage
$dateDebut = isset($_GET["date_debut"]) ? $_GET["date_debut"] : null;
$dateFin = isset($_GET["date_fin"]) ? $_GET["date_fin"] : null;

$result = getTotauxFacturesEtPaiements($campusDonnee, $connexion, $dateDebut, $dateFin);
?>

<!DOCTYPE html>
<?php include('../../head.php'); ?>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campuscoud - Recettes</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="./global.css">
</head>

<body>
    <div class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave me-2"></i>Gestion des Recettes</h1>
                <span class="badge bg-light text-dark fs-6">
                    <i class="fas fa-building me-1"></i> <?= htmlspecialchars($campusDonnee) ?>
                </span>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="get" action="global" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Campus</label>
                        <select class="form-select" name="campus" required>
                            <option value="global">Tous les campus</option>
                            <?php foreach ($campus as $cam): ?>
                            <option value="<?= htmlspecialchars($cam) ?>" <?= ($cam == $campusDonnee) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cam) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Date début</label>
                        <input type="date" name="date_debut" class="form-control" value="<?= htmlspecialchars($dateDebut) ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Date fin</label>
                        <input type="date" name="date_fin" class="form-control" value="<?= htmlspecialchars($dateFin) ?>">
                    </div>
                    
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1 py-3">
                            <i class="fas fa-filter me-1"></i> Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau des recettes -->
        <div class="table-container">
            <table class="recette-table">
                <thead>
                    <tr style="font-family: 'Poppins', sans-serif !important;font-size:19px;">
                        <th>Libellé</th>
                        <th>Montant (F CFA)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="font-family: 'Poppins', sans-serif !important;font-size:19px;">
                        <td><strong>Total Facturé</strong></td>
                        <td style="font-family: 'Poppins', sans-serif !important;font-size:19px !important;" class="total-facture"><?= number_format($result['total_facture'], 0, ',', ' ') ?></td>
                    </tr>
                    <tr style="font-family: 'Poppins', sans-serif !important;font-size:19px;">
                        <td><strong>Loyer Payé</strong></td>
                        <td style="font-family: 'Poppins', sans-serif !important;font-size:19px !important;" class="loyer-paye"><?= number_format($result['total_loyer_paye'], 0, ',', ' ') ?></td>
                    </tr>
                    <tr style="font-family: 'Poppins', sans-serif !important;font-size:19px !important;">
                        <td><strong>Caution Payée</strong></td>
                        <td style="font-family: 'Poppins', sans-serif !important;font-size:19px !important;" class="caution-payee"><?= number_format($result['total_caution_payee'], 0, ',', ' ') ?></td>
                    </tr>
                    <tr style="font-family: 'Poppins', sans-serif !important;font-size:19px;">
                        <td><strong>Total Payé</strong></td>
                        <td style="font-family: 'Poppins', sans-serif !important;font-size:19px !important;" class="total-paye"><?= number_format($result['total_paye'], 0, ',', ' ') ?></td>
                    </tr>
                    <tr style="font-family: 'Poppins', sans-serif !important;font-size:19px;">
                        <td><strong>Reste à Payer</strong></td>
                        <td style="font-family: 'Poppins', sans-serif !important;font-size:19px !important;" class="reste-payer"><?= number_format($result['reste_total'], 0, ',', ' ') ?></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="d-flex justify-content-end mt-4">
                <button onclick="exportToExcel()" class="btn btn-excel">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </button>
            </div>
        </div>

        <div class="text-center mt-4">
            <button class="btn btn-outline-secondary py-2 px-4" onclick="history.back()">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </button>
        </div>
    </div>

    <!-- Script pour l'export Excel -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.19.3/package/dist/xlsx.full.min.js"></script>
    
    <script>
    function exportToExcel() {
        // Création du workbook
        const wb = XLSX.utils.book_new();
        
        // Données à exporter
        const data = [
            ["Libellé", "Montant (F CFA)"],
            ["Total Facturé", "<?= number_format($result['total_facture'], 0, ',', ' ') ?>"],
            ["Loyer Payé", "<?= number_format($result['total_loyer_paye'], 0, ',', ' ') ?>"],
            ["Caution Payée", "<?= number_format($result['total_caution_payee'], 0, ',', ' ') ?>"],
            ["Total Payé", "<?= number_format($result['total_paye'], 0, ',', ' ') ?>"],
            ["Reste à Payer", "<?= number_format($result['reste_total'], 0, ',', ' ') ?>"]
        ];
        
        // Création de la feuille
        const ws = XLSX.utils.aoa_to_sheet(data);
        
        // Ajout de la feuille au workbook
        XLSX.utils.book_append_sheet(wb, ws, "Recettes");
        
        // Génération du fichier Excel
        const fileName = `Recettes_<?= htmlspecialchars($campusDonnee) ?>_${new Date().toISOString().slice(0,10)}.xlsx`;
        XLSX.writeFile(wb, fileName);
    }
    </script>
</body>
</html>