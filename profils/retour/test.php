<?php
session_start();
include ('../../traitement/fonction.php');
$fac = isset($_GET['fac']) ? htmlspecialchars($_GET['fac']) : 'E.S.P';
//$result = getEtuNonAffByFac_2($fac);
$result2 = getEtuNonAffByFac_3($fac);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test</title>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    
</body>
</html>
<div class="container" style="width:50%;">
    <form method="get" action="test">
        <div class="row align-items-center justify-content-center">
            <div class="col-5">
                <select name="fac" class="fac" required>
                    <option value="">-- Choisir une faculté --</option>

                    <?php foreach (getAllEtablissement() as $e): ?>
                    <option value="<?= $e['etablissement']; ?>"
                        <?= (isset($_GET['fac']) && $_GET['fac'] == $e['etablissement']) ? 'selected' : '' ?>>
                        <?= $e['etablissement']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-5">
                <button type="submit" class="btn btn-primary pavillon"><strong>Rechercher</strong></button>
            </div>
        </div>
    </form>
</div>

<?php
//var_dump($result);
var_dump($result2);
?>