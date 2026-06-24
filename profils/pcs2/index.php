<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: https://campuscoud.com/');
    exit();
}

$fac = $_SESSION['fac']; 
include('../../traitement/fonction.php');
verif_type_mdp_2($_SESSION['username']);

$faculter = $fac . "/SOCIALE";
$lits = getLitByPcs($faculter);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>

    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <style>
    .lit-button {
        height: 50px;
        margin: 5px;
        text-align: center;
        border-radius: 8px;
        font-weight: bold;
        font-size: 18px;
        width: 90px;
    }

    .lit-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
        margin-top: 30px;
    }
    </style>
</head>

<body>
    <?php include('../../head.php'); ?>
    <br>

    <h3 class="text-center mb-4">
        Lits disponibles pour le quota social de la faculté <strong><?= htmlspecialchars($fac) ?></strong>.
    </h3>

    <div class="container">
        <h4 class="text-center mb-4">Lits Trouvés</h4>

        <div class="row">
            <!-- ========================== COLONNE FILLE ========================== -->
            <div class="col-md-6" style="border-right: 2px solid #ccc;">
                <h5 class="text-center text-danger mb-3">Filles</h5>
                <div class="lit-container">
                    <?php 
                $hasFille = false;

                if (!empty($lits) && is_array($lits)) {
                    foreach ($lits as $litItem) {

                        $value = is_array($litItem) ? ($litItem['lit'] ?? reset($litItem)) : $litItem;
                        $value = trim((string)$value);
                        if ($value === '') continue;

                        $sexeLit = strtolower(getSexeLit($value)); 

                        if ($sexeLit !== "f") continue; // seulement fille

                        $hasFille = true; // au moins un lit fille trouvé

                        // occupants
                        $occupants = getOccupantsByLitName($value);
                        $nbOcc = count($occupants);
                        $first = $occupants[0] ?? null;
                        $occupantName = $first ? trim(($first['prenoms'] ?? '') . ' ' . ($first['nom'] ?? '')) : null;

                        // individuel ?
                        $isIndiv = isLitIndividuel($value);

                        // couleur
                        if ($isIndiv && $nbOcc >= 1)        { $class = "btn btn-danger";  $tooltip = $occupantName ?: "Occupé"; }
                        elseif (!$isIndiv && $nbOcc >= 2)  { $class = "btn btn-danger";  $tooltip = "$nbOcc occupants"; }
                        elseif (!$isIndiv && $nbOcc == 1)  { $class = "btn btn-warning"; $tooltip = "1 occupant : $occupantName"; }
                        elseif ($isIndiv && $nbOcc == 0)   { $class = "btn btn-warning"; $tooltip = "Lit individuel (libre)"; }
                        else                                { $class = "btn btn-primary"; $tooltip = "Lit libre"; }

                        $url = "pageLit?lit=" . urlencode($value);
                ?>
                    <button class="<?= $class ?> lit-button" title="<?= htmlspecialchars($tooltip) ?>"
                        onclick="window.location.href='<?= $url ?>'">
                        <?= htmlspecialchars($value) ?>
                    </button>
                    <?php
                    }
                }

                if (!$hasFille) {
                    echo '<p class="text-center text-muted w-100">Aucun lit trouvé pour les filles.</p>';
                }
                ?>
                </div>
            </div>

            <!-- ========================== COLONNE GARÇON ========================== -->
            <div class="col-md-6">
                <h5 class="text-center text-primary mb-3">Garçons</h5>
                <div class="lit-container">
                    <?php 
                $hasGarcon = false;

                if (!empty($lits) && is_array($lits)) {
                    foreach ($lits as $litItem) {

                        $value = is_array($litItem) ? ($litItem['lit'] ?? reset($litItem)) : $litItem;
                        $value = trim((string)$value);
                        if ($value === '') continue;

                        $sexeLit = strtolower(getSexeLit($value)); 

                        if ($sexeLit !== "g") continue; // seulement garçon

                        $hasGarcon = true;

                        // occupants
                        $occupants = getOccupantsByLitName($value);
                        $nbOcc = count($occupants);
                        $first = $occupants[0] ?? null;
                        $occupantName = $first ? trim(($first['prenoms'] ?? '') . ' ' . ($first['nom'] ?? '')) : null;

                        $isIndiv = isLitIndividuel($value);

                        // couleur
                        if ($isIndiv && $nbOcc >= 1)        { $class = "btn btn-danger";  $tooltip = $occupantName ?: "Occupé"; }
                        elseif (!$isIndiv && $nbOcc >= 2)  { $class = "btn btn-danger";  $tooltip = "$nbOcc occupants"; }
                        elseif (!$isIndiv && $nbOcc == 1)  { $class = "btn btn-warning"; $tooltip = "1 occupant : $occupantName"; }
                        elseif ($isIndiv && $nbOcc == 0)   { $class = "btn btn-warning"; $tooltip = "Lit individuel (libre)"; }
                        else                                { $class = "btn btn-success"; $tooltip = "Lit libre"; }

                        $url = "pageLit?lit=" . urlencode($value);
                ?>
                    <button class="<?= $class ?> lit-button" title="<?= htmlspecialchars($tooltip) ?>"
                        onclick="window.location.href='<?= $url ?>'">
                        <?= htmlspecialchars($value) ?>
                    </button>
                    <?php
                    }
                }

                if (!$hasGarcon) {
                    echo '<p class="text-center text-muted w-100">Aucun lit trouvé pour les garçons.</p>';
                }
                ?>
                </div>
            </div>
        </div>
    </div>


    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>

</body>

</html>