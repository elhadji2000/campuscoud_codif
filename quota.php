<?php session_start();   
include('head.html'); 
?>

<!DOCTYPE html>
<html lang="fr">

<!--head> 

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="assets/css/vendor.css" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <link rel="stylesheet" href="assets/css/login.css" />
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="assets/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="assets/js/modernizr.js"></script>
    <script src="assets/js/pace.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<?php //include('head.html'); ?>
</head-->

<?php
include('traitement/fonction.php'); 
// Récupérer les résultats avec pagination
$resultats = getLitsBySexeAndNiveau2();

$lits = $resultats['lits'];
$totaux = $resultats['totaux'];
$pagination = $resultats['pagination'];

// Vérifiez si 'totauxParEtablissement' existe
if (isset($resultats['totauxParEtablissement'])) {
    $totauxParEtablissement = $resultats['totauxParEtablissement'];
} else {
    $totauxParEtablissement = []; // Initialiser par défaut
}
?>

<body>
    <?php //include('../head.php'); ?>
    <div class="container mt-2">
        <h1>
            <br>
            <center>Liste des Quotas</center> <br>
        </h1>

        <!-- Liens de pagination -->
        <nav aria-label="Navigation de pagination">
            <ul class="pagination justify-content-center">
                <?php if ($pagination['current_page'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>" aria-label="Précédent">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>" aria-label="Suivant">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Tableau des quotas -->
        <table class="table table-bordered" style="border: 2px solid black;">
            <thead>
                <tr style="background-color: rgb(255 255 255 / 0.74); border: 1px solid black;">
                    <th style="border: 1px solid black;">
                        <h4>Facultés</h4>
                    </th>
                    <th style="border: 1px solid black;">
                        <h4>Niveaux Et Formations</h4>
                    </th>
                    <th style="border: 1px solid black;">
                        <h4>Garçons</h4>
                    </th>
                    <th style="border: 1px solid black;">
                        <h4>Filles</h4>
                    </th>
                    <th style="border: 1px solid black;">
                        <h4>Total Quotas Par Niveau</h4>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($lits as $etablissement => $niveaux):
                    $firstRow = true;
                    $rowCount = count($niveaux);
                    foreach ($niveaux as $niveau => $data):
                        if ($firstRow):
                ?>
                            <tr style="background-color: #3777b0; border: 1px solid black;">
                                <td rowspan="<?php echo $rowCount; ?>" style="text-align: center; vertical-align: middle; font-size: 16px; border: 1px solid black;">
                                    <h4><?php echo htmlspecialchars($etablissement); ?></h4>
                                </td>
                                <td style="border: 1px solid black;">
                                    <h4><?php echo htmlspecialchars($niveau); ?></h4>
                                </td>
                                <td style="border: 1px solid black;">
                                    <h4><?php echo htmlspecialchars($data['garçons']); ?></h4>
                                </td>
                                <td style="border: 1px solid black;">
                                    <h4><?php echo htmlspecialchars($data['filles']); ?></h4>
                                </td>
                                <td style="border: 1px solid black;">
                                    <h4><?php echo htmlspecialchars($data['total']); ?></h4>
                                </td>
                            </tr>
                        <?php
                            $firstRow = false;
                        else:
                        ?>
                            <tr style="background-color: #3777b0; border: 1px solid black;">
                                <td style="border: 1px solid black;">
                                    <h4><?php echo htmlspecialchars($niveau); ?></h4>
                                </td>
                                <td style="border: 1px solid black;">
                                    <h4><?php echo htmlspecialchars($data['garçons']); ?></h4>
                                </td>
                                <td style="border: 1px solid black;">
                                    <h4><?php echo htmlspecialchars($data['filles']); ?></h4>
                                </td>
                                <td style="border: 1px solid black;">
                                    <h4><?php echo htmlspecialchars($data['total']); ?></h4>
                                </td>
                            </tr>
                    <?php
                        endif;
                    endforeach; ?>

                    <!-- Totaux par établissement -->
                    <tr style="background-color:rgb(255, 255, 255, 0.74); border: 1px solid black;">
                        <td style="border: 1px solid black;"><strong>
                                <h4>Total quota <?php echo htmlspecialchars($etablissement); ?></h4>
                            </strong></td>
                        <td style="border: 1px solid black;"></td>
                        <td style="border: 1px solid black;"><strong><?php echo htmlspecialchars($totauxParEtablissement[$etablissement]['garçons'] ?? 0); ?></strong></td>
                        <td style="border: 1px solid black;"><strong><?php echo htmlspecialchars($totauxParEtablissement[$etablissement]['filles'] ?? 0); ?></strong></td>
                        <td style="border: 1px solid black;"><strong>
                                <?php
                                $totalEtablissement = ($totauxParEtablissement[$etablissement]['garçons'] ?? 0) +
                                    ($totauxParEtablissement[$etablissement]['filles'] ?? 0);
                                echo htmlspecialchars($totalEtablissement);
                                ?>
                            </strong></td>
                    </tr>

                <?php endforeach; ?>
                <tr style="background-color:rgb(255, 255, 255, 0.74); border: 1px solid black;">
                    <td style="border: 1px solid black;"><strong>
                            <h4>Total Global</h4>
                        </strong></td>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;"><strong><?php echo htmlspecialchars($totaux['garçons']); ?></strong></td>
                    <td style="border: 1px solid black;"><strong><?php echo htmlspecialchars($totaux['filles']); ?></strong></td>
                    <td style="border: 1px solid black;"><strong><?php echo htmlspecialchars($totaux['total']); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php //include('../foot.html'); ?>
</body>

<script src="../../assets/js/script.js"></script>
<script src="../../assets/js/jquery-3.2.1.min.js"></script>
<script src="../../assets/js/plugins.js"></script>
<script src="../../assets/js/main.js"></script>

</html>