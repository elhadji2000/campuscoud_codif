<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: https://campuscoud.com/');
    exit();
}

//$fac = $_SESSION['fac']; 
include('../../traitement/fonction.php');
verif_type_mdp_2($_SESSION['username']);
?>
<?php
$faculters = getAllEtablissement_1();

// faculté sélectionnée
$selectedFac = $_GET['fac'] ?? $faculters[0];

// construction quota social
$faculter = $selectedFac;
$_SESSION['fac'] = $faculter;
// recharge lits selon fac
$lits = getLitByPcs($faculter);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="log.gif" type="image/x-icon">
    <link rel="icon" href="log.gif" type="image/x-icon">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.bundle.min.js">
</head>

<body>
    <?php include('../../head.php'); ?>

    <head>

        <style>
        .lit-button {
            height: 70px;
            margin: 5px;
            text-align: center;
            border-radius: 8px;
            font-weight: bold;
            font-size: 22px;
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
    <div class="container mt-3 mb-4">

        <form method="GET" class="row justify-content-center g-2">

            <!-- SEARCH INPUT -->
            <!-- <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Rechercher un lit..."
                value="<?= $_GET['search'] ?? '' ?>">
        </div> -->

            <!-- SELECT FACULTÉ -->
            <div class="col-md-4">
                <select name="fac" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($faculters as $facItem): ?>
                    <option value="<?= $facItem ?>" <?= ($selectedFac == $facItem) ? 'selected' : '' ?>>
                        <?= $facItem ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- BUTTON -->
            <!-- <div class="col-md-2">
            <button class="btn btn-primary w-100">
                Rechercher
            </button>
        </div> -->
        </form>

    </div>
    <div class="container mt-4">

        <!-- TITRE PRINCIPAL -->
        <div class="text-center mb-4">
            <h4 class="fw-bold text-dark">
                Lits disponibles pour le quota social
            </h4>
            <span class="text-muted">
                Faculté : <strong><?= htmlspecialchars($faculter) ?></strong>
            </span>
        </div>

        <!-- SECTION -->
        <div class="row g-4">

            <!-- ================= FILLE ================= -->
            <div class="col-md-6">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary text-white text-center fw-bold">
                        Filles
                    </div>

                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2 justify-content-center">

                            <?php 
                        $hasFille = false;

                        if (!empty($lits) && is_array($lits)) {
                            foreach ($lits as $litItem) {

                                $value = is_array($litItem) ? ($litItem['lit'] ?? reset($litItem)) : $litItem;
                                $value = trim((string)$value);
                                if ($value === '') continue;

                                if (strtolower(getSexeLit($value)) !== "f") continue;

                                $hasFille = true;

                                $occupants = getOccupantsByLitName($value);
                                $nbOcc = count($occupants);

                                $first = $occupants[0] ?? null;
                                $occupantName = $first ? trim(($first['prenoms'] ?? '') . ' ' . ($first['nom'] ?? '')) : null;

                                $isIndiv = isLitIndividuel($value);

                                if ($isIndiv && $nbOcc >= 1)        { $class = "btn btn-danger btn-lg"; }
                                elseif (!$isIndiv && $nbOcc >= 2)  { $class = "btn btn-danger btn-lg"; }
                                elseif (!$isIndiv && $nbOcc == 1)  { $class = "btn btn-warning btn-lg"; }
                                elseif ($isIndiv && $nbOcc == 0)   { $class = "btn btn-warning btn-lg"; }
                                else                                { $class = "btn btn-primary btn-lg"; }

                                $url = "pageLit?lit=" . urlencode($value);
                        ?>

                            <button class="<?= $class ?>" onclick="window.location.href='<?= $url ?>'"
                                title="<?= htmlspecialchars($occupantName ?? 'Lit') ?>">
                                <?= htmlspecialchars($value) ?>
                            </button>

                            <?php }} ?>

                            <?php if (!$hasFille): ?>
                            <i class="text-muted text-center w-100">Aucun lit disponible</i>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

            </div>

            <!-- ================= GARÇON ================= -->
            <div class="col-md-6">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary text-white text-center fw-bold">
                        Garçons
                    </div>

                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2 justify-content-center">

                            <?php 
                        $hasGarcon = false;

                        if (!empty($lits) && is_array($lits)) {
                            foreach ($lits as $litItem) {

                                $value = is_array($litItem) ? ($litItem['lit'] ?? reset($litItem)) : $litItem;
                                $value = trim((string)$value);
                                if ($value === '') continue;

                                if (strtolower(getSexeLit($value)) !== "g") continue;

                                $hasGarcon = true;

                                $occupants = getOccupantsByLitName($value);
                                $nbOcc = count($occupants);

                                $first = $occupants[0] ?? null;
                                $occupantName = $first ? trim(($first['prenoms'] ?? '') . ' ' . ($first['nom'] ?? '')) : null;

                                $isIndiv = isLitIndividuel($value);

                                if ($isIndiv && $nbOcc >= 1)        { $class = "btn btn-danger btn-lg"; }
                                elseif (!$isIndiv && $nbOcc >= 2)  { $class = "btn btn-danger btn-lg"; }
                                elseif (!$isIndiv && $nbOcc == 1)  { $class = "btn btn-warning btn-lg"; }
                                elseif ($isIndiv && $nbOcc == 0)   { $class = "btn btn-warning btn-lg"; }
                                else                                { $class = "btn btn-success btn-lg"; }

                                $url = "pageLit?lit=" . urlencode($value);
                        ?>

                            <button class="<?= $class ?>" onclick="window.location.href='<?= $url ?>'"
                                title="<?= htmlspecialchars($occupantName ?? 'Lit') ?>">
                                <?= htmlspecialchars($value) ?>
                            </button>

                            <?php }} ?>

                            <?php if (!$hasGarcon): ?>
                            <i class="text-muted text-center w-100">Aucun lit disponible</i>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

</body>

</html>