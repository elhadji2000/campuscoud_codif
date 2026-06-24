<?php session_start(); ?>
<html lang="fr">
<?php 

include ('../../traitement/fonction.php');

$link = connexionBD();

if (!isset($_SESSION['profil'])) {
    header("location: ../../");
    exit();
}

include('../../activite.php'); 

// Récupération du numéro de chambre
if (isset($_GET['ch'])) {
    $numch = trim($_GET['ch']);
} elseif (isset($_POST['ch'])) {
    $numch = trim($_POST['ch']);
} else {
    $numch = null;
}

$lits = getLitParChambre($link, $numch);
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

    <section id="styles" class="s-styles">
        <center>
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <label for="search" style="font-weight: bold; font-size: 16px; color: #333;">
                    🔍 Rechercher une chambre :
                </label>
            </div>

            <form method="GET" action="details_ch" class="row g-3"
                style="display: flex; justify-content: center; margin-top: 20px;">
                <div class="col-auto">
                    <input type="text" name="ch" class="form-control" placeholder="Saisir Numero Chambre..."
                        style="padding:3px;">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success mb-3"
                        style="background-color: #676eebff;">Rechercher</button>
                </div>
            </form>

            <div class="table-responsive container" style="margin-top:20px; width:70%;text-align:center;">
                <table border="1" class="table table-bordered">
                    <thead>
                        <tr style="background-color:#f2f2f2;">
                            <th>#</th>
                            <th>ID_Lit</th>
                            <th>Nom_Lit</th>
                            <th>NiveauFormation</th>
                            <th>Choisi par:</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($numch)) : ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">

                            </td>
                        </tr>
                        <?php elseif (!empty($lits)) : ?>
                        <?php $i = 1; ?>
                        <?php foreach ($lits as $lit) : ?>
                        <tr>
                            <td style="text-align:center;"><?= $i++; ?></td>
                            <td><?= htmlspecialchars($lit['id_lit']); ?></td>
                            <td><?= htmlspecialchars($lit['lit']); ?></td>
                            <td style="color:<?= !empty($lit['niveauFormation']) ? 'black' : 'gray'; ?>">
                                <?= !empty($lit['niveauFormation']) ? $lit['niveauFormation'] : 'vide'; ?>
                            </td>
                            <td style="color:<?= !empty($lit['prenom']) ? 'black' : 'gray'; ?>">
                                <?= !empty($lit['prenoms']) ? $lit['prenoms'].' '.$lit['nom'].' ( '.$lit['num_etu'].' )' : 'vide'; ?>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">
                                ❌ Aucun lit trouvé pour la chambre <?= htmlspecialchars($numch); ?>.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </center>
    </section>

    <center>
        <a href="javascript:history.back()" id="retour">Retour</a><br><br>
    </center>
</body>

</html>