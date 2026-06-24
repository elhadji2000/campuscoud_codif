<?php session_start(); ?>
<html lang="fr">
<?php 
include ('../../traitement/fonction.php');

$link = connexionBD();

if (!isset($_SESSION['retour'])) {
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

$lits = getLitEtudiant($link, $numch);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>

    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/base.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
    select.fac {
        width: 250px;
        height: 50px;
        font-size: 16px;
        padding: 5px;
        border-radius: 5px;
    }

    table td {}

    #floatingBtn:hover {
        background-color: #0056b3;
        transform: scale(1.1);
    }

    #sendBtn {
        border-radius: 50%;
        width: 45px;
        height: 45px;
    }
    </style>
</head>
<?php include('../../head.php'); ?>

<body>

    <section id="styles" class="s-styles">
        <center>
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <label for="search" style="font-weight: bold; font-size: 16px; color: #333;">
                    🔍 Rechercher Lit :
                </label>
            </div>
            <?php

            if (isset($_SESSION['message'])) {
                $type = $_SESSION['message_type']; 
                echo "<div class='alert alert-$type text-center'>"
                    . $_SESSION['message'] . 
                    "</div>";

                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
        ?>
            <form method="GET" action="remplacer" class="row g-3"
                style="display: flex; justify-content: center; margin-top: 20px;">
                <div class="col-auto">
                    <input type="text" name="ch" class="form-control" placeholder="EX: 2A_ESP_1" style="padding:3px;">
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
                            <th>ID</th>
                            <th>Lit</th>
                            <th>NiveauFormation</th>
                            <th>Etudiant</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($numch)) : ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">
                                Veuillez entrer un numéro de chambre pour lancer la recherche.
                            </td>
                        </tr>
                        <?php elseif (!empty($lits)) : ?>
                        <?php $i = 1; ?>
                        <?php foreach ($lits as $lit) : ?>
                        <tr>
                            <td style="text-align:center;"><?= $i++; ?></td>
                            <td><?= htmlspecialchars($lit['id_lit']??''); ?></td>
                            <td><?= htmlspecialchars($lit['lit']); ?></td>
                            <td style="color:<?= !empty($lit['niveauFormation']) ? 'black' : 'gray'; ?>">
                                <?= !empty($lit['niveauFormation']) ? $lit['niveauFormation'] : 'vide'; ?>
                            </td>
                            <td style="color:<?= !empty($lit['prenom']) ? 'black' : 'gray'; ?>">
                                <?= !empty($lit['prenoms']) ? $lit['prenoms'].' '.$lit['nom'].' ( '.$lit['num_etu'].' )' : 'vide'; ?>
                            </td>
                            <td><a class="text-decoration-underline"
                                    href="trait_rempl?id_etu=<?= htmlspecialchars($lit['id_etu']??''); ?>&id_lit=<?= htmlspecialchars($lit['id_lit']??''); ?>&numero=<?= htmlspecialchars($lit['num_etu']??''); ?>">
                                    traiter</a></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">
                                ❌ Aucun resultat .
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
num_etu = 20220B5YL   
id_lit = 12648
115J_2
</html>

