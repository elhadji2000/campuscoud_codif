<?php
// Connexion à la base de données
include('../../traitement/fonction.php');
connexionBD();
session_start();

// Configuration de la pagination
$itemsPerPage = 100;

// Récupération des filtres
$date_debut = $_GET['date_debut'] ?? '';
$date_fin = $_GET['date_fin'] ?? '';
$username = $_GET['regisseur'] ?? '';
$libelle = $_GET['libelle'] ?? '';
$pavillons = getAllPavillons($connexion);
$pavillonDonne = isset($_GET["pavillon"]) ? $_GET["pavillon"] : htmlspecialchars($pavillons[0]);
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Récupération des données
$data = getPaiementWithDateInterval_4($date_debut, $date_fin, $username, $libelle, $pavillonDonne, $page = 1, $limit = 100);
$tabPaiement = $data['data'];
$totalMontant = $data['totalMontant'];
$totalPages = $data['totalPages'];
$currentPage = $data['currentPage'];

// Calcul des totaux
$Total = calculateMontantTotal();
$cautionSum = calculateCautionSum();
$mens = ($Total - $cautionSum);
$regisseurs = getAllRegisseurs($connexion);
?>

<!DOCTYPE html>
<html lang='fr'>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD - État des Encaissements</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            background-color: #f5f7fa;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: none;
        }
        
        .card-header {
            background-color: var(--secondary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
            font-weight: 600;
        }
        
        .form-control, .form-select {
            border-radius: 5px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-warning {
            background-color: #f39c12;
            border-color: #f39c12;
            color: white;
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table th {
            background-color: var(--secondary-color);
            color: white;
            font-weight: 500;
            text-align: center;
            vertical-align: middle;
        }
        
        .table td {
            vertical-align: middle;
            text-align: center;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .pagination .page-link {
            color: var(--secondary-color);
        }
        
        .total-box {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
        }
        
        .total-label {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }
        
        .total-value {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .page-title {
            color: var(--secondary-color);
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
            
            .form-control, .form-select {
                margin-bottom: 10px;
            }
        }
    </style>
    
    <?php include('../../head.php'); ?>
</head>

<body>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="page-title">ETAT DES ENCAISSEMENTS PÉRIODIQUES</h1>
            </div>
        </div>

        <!-- Filtres de recherche -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter me-2"></i>Filtres de recherche
            </div>
            <div class="card-body">
                <form action="requestEtatPaiement_cs.php" method="POST" onsubmit="return validateForm()">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="date_debut" class="form-label">Date début</label>
                            <input type="date" id="date_debut" name="date_debut" class="form-control" 
                                   value="<?= htmlspecialchars($date_debut) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="date_fin" class="form-label">Date fin</label>
                            <input type="date" id="date_fin" name="date_fin" class="form-control" 
                                   value="<?= htmlspecialchars($date_fin) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="regisseur" class="form-label">Pavillon</label>
                            <select class="form-select pavillon-select" name="pavillon" required>
                                <option value="">Sélectionnez un pavillon...</option>
                                <?php foreach ($pavillons as $pavillon): ?>
                                <option value="<?= htmlspecialchars($pavillon) ?>"
                                    <?= ($pavillon == $pavillonDonne) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pavillon) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="libelle" class="form-label">Type de paiement</label>
                            <select class="form-select" name="libelle" id="libelle">
                                <option value="">Tous les types</option>
                                <option value="CAUTION" <?= ($libelle === 'CAUTION') ? 'selected' : '' ?>>CAUTION</option>
                                <option value="LOYER" <?= ($libelle === 'LOYER') ? 'selected' : '' ?>>LOYER</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6 d-flex justify-content-start">
                            <button type="submit" name="rechercher" class="btn btn-primary me-2">
                                <i class="fas fa-search me-2"></i>Rechercher
                            </button>
                            <a href="convention/paiementPdf.php" class="btn btn-outline-secondary">
                                <i class="fas fa-print me-2"></i>Imprimer
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Totaux -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="total-box">
                    <div class="total-label">Total filtré</div>
                    <div class="total-value"><?= number_format($totalMontant, 0, ', ', ' ') ?> F CFA</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="total-box">
                    <div class="total-label">Total caution</div>
                    <div class="total-value"><?= number_format($cautionSum, 0, ', ', ' ') ?> F CFA</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="total-box">
                    <div class="total-label">Total loyer</div>
                    <div class="total-value"><?= number_format($mens, 0, ', ', ' ') ?> F CFA</div>
                </div>
            </div>
        </div>

        <!-- Tableau des résultats -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-table me-2"></i>Résultats des paiements
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>N° Quittance</th>
                                <th>Date paiement</th>
                                <th>Libellé</th>
                                <th>N° Carte</th>
                                <th>Prénom</th>
                                <th>Nom</th>
                                <th>Montant</th>
                                <th>Régisseur</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tabPaiement)): ?>
                                <?php foreach ($tabPaiement as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['quittance']) ?></td>
                                        <td><?= htmlspecialchars($row['dateTime_paie']) ?></td>
                                        <td>
                                            <?php
                                                if (isset($_SESSION['libelle']) && strtoupper(trim($_SESSION['libelle'])) === 'CAUTION') {
                                                    echo 'CAUTION';
                                                } else {
                                                    $libelleParts = explode(',', $row['libelle']);
                                                    $filtered = array_filter($libelleParts, function($part) {
                                                        return strtoupper(trim($part)) !== 'CAUTION';
                                                    });
                                                    echo htmlspecialchars(implode(', ', $filtered));
                                                }
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['num_etu']) ?></td>
                                        <td><?= htmlspecialchars($row['prenoms']) ?></td>
                                        <td><?= htmlspecialchars($row['nom']) ?></td>
                                        <td>
                                            <?php
                                                $montant = $row['montant'];
                                                if (isset($_SESSION['libelle'])) {
                                                    $sessionLibelle = strtoupper(trim($_SESSION['libelle']));
                                                    if ($sessionLibelle === 'CAUTION') {
                                                        $montant = 5000;
                                                    } elseif ($sessionLibelle === 'LOYER' && stripos($row['libelle'], 'CAUTION') !== false) {
                                                        $montant -= 5000;
                                                    }
                                                }
                                                echo number_format($montant, 0, ', ', ' ');
                                            ?> F CFA
                                        </td>
                                        <td><?= htmlspecialchars($row['username_user']) ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editModal<?= $row['id_paie'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal de modification -->
                                    <div class="modal fade" id="editModal<?= $row['id_paie'] ?>" tabindex="-1"
                                        aria-labelledby="editModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Modifier le paiement</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="etatPaiement_cs.php" method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id_paie" value="<?= $row['id_paie'] ?>">
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Numéro étudiant</label>
                                                            <input type="text" class="form-control"
                                                                value="<?= htmlspecialchars($row['num_etu']) ?>" readonly>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Montant</label>
                                                            <select name="montant" class="form-select" required>
                                                                <option value="" disabled selected>Sélectionnez un montant</option>
                                                                <?php
                                                                $montants = [
                                                                    3000, 4000, 5000, 6000, 8000, 9000, 
                                                                    11000, 12000, 13000, 15000, 16000, 
                                                                    17000, 18000, 20000, 21000, 24000, 
                                                                    27000, 28000, 30000, 32000, 36000, 40000
                                                                ];
                                                                
                                                                foreach ($montants as $m) {
                                                                    $selected = ($row['montant'] == $m) ? 'selected' : '';
                                                                    echo "<option value='$m' $selected>" . number_format($m, 0, ', ', ' ') . " F CFA</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label">Libellé</label>
                                                            <input type="text" name="libelle" class="form-control"
                                                                value="<?= htmlspecialchars($row['libelle']) ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4">Aucun résultat trouvé</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Pagination">
                        <ul class="pagination justify-content-center mt-4">
                            <?php
                            $params = http_build_query([
                                'date_debut' => $date_debut,
                                'date_fin' => $date_fin,
                                'regisseur' => $username,
                                'libelle' => $libelle
                            ]);
                            ?>
                            
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= $params ?>&page=<?= $currentPage - 1 ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php 
                            $start = max(1, $currentPage - 2);
                            $end = min($totalPages, $currentPage + 2);
                            
                            if ($start > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?'.$params.'&page=1">1</a></li>';
                                if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            
                            for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?= ($i === $currentPage) ? 'active' : '' ?>">
                                    <a class="page-link" href="?<?= $params ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php 
                            if ($end < $totalPages) {
                                if ($end < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                echo '<li class="page-item"><a class="page-link" href="?'.$params.'&page='.$totalPages.'">'.$totalPages.'</a></li>';
                            }
                            ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= $params ?>&page=<?= $currentPage + 1 ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/script.js"></script>
    
    <script>
        function validateForm() {
            const dateDebut = document.getElementById('date_debut').value;
            const dateFin = document.getElementById('date_fin').value;
            const dateMin = new Date('2024-01-01');

            if (dateDebut) {
                const debut = new Date(dateDebut);
                if (debut < dateMin) {
                    alert("La date de début doit être supérieure au 31/12/2023.");
                    return false;
                }
            }

            if (dateFin) {
                const fin = new Date(dateFin);

                if (!dateDebut && fin < dateMin) {
                    alert("Si la date de début n'est pas renseignée, la date de fin doit être le 01/01/2024 ou après.");
                    return false;
                }

                if (dateDebut) {
                    const debut = new Date(dateDebut);
                    if (fin < debut) {
                        alert("La date de fin doit être postérieure ou égale à la date de début.");
                        return false;
                    }
                }
            }

            return true;
        }
        
        // Mise en évidence des champs de filtrage utilisés
        $(document).ready(function() {
            const filters = ['date_debut', 'date_fin', 'regisseur', 'libelle'];
            filters.forEach(filter => {
                if (getParameterByName(filter)) {
                    $(`#${filter}`).addClass('border border-primary');
                }
            });
        });
        
        function getParameterByName(name) {
            const url = new URL(window.location.href);
            return url.searchParams.get(name);
        }
    </script>
</body>
</html>