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
    input.fac {
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

            <div class="container" style="width:50%;">
                <form method="get" action="remplacer">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-5">
                            <input type="text" name="ch" class="fac" placeholder="EX: 2A_ESP_1" style="padding:3px;">
                        </div>
                        <div class="col-5">
                            <button type="submit" class="btn btn-primary pavillon"><strong>Rechercher</strong></button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive container" style="margin-top:20px; width:90%;text-align:center;">
                <table border="1" class="table table-bordered">
                    <thead>
                        <tr style="background-color:#f2f2f2;">
                            <th>#</th>
                            <th>ID</th>
                            <th>Lit</th>
                            <th>NiveauFormation</th>
                            <th>Etudiant</th>
                            <th>Num_Etu</th>
                            <th>Sexe</th>
                            <th>Telephone</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($numch)) : ?>
                        <tr>
                            <td colspan="9" style="text-align:center;">
                                Veuillez entrer un numéro de LIT pour lancer la recherche.
                            </td>
                        </tr>

                        <?php elseif (!empty($lits)) : ?>
                        <?php $i = 1; ?>
                        <?php foreach ($lits as $lit) : ?>

                        <!-- ================= TITULAIRE ================= -->
                        <tr>
                            <td style="text-align:center;"><?= $i++; ?></td>
                            <td><?= htmlspecialchars($lit['id_lit'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($lit['lit']); ?></td>
                            <td><?= htmlspecialchars($lit['niveauFormation'] ?? 'vide'); ?></td>
                            <td>
                                <?= htmlspecialchars(($lit['prenoms'] ?? '') . ' ' . ($lit['nom'] ?? '')); ?>
                            </td>
                            <td><?= htmlspecialchars($lit['num_etu'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($lit['sexe'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($lit['telephone'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($lit['statut'] ?? 'titulaire'); ?></td>
                            <td>
                                <a class="text-decoration-underline"
                                    onclick="return confirm('Êtes-vous sûr de vouloir continuer ?');"
                                    href="trait_rempl?id_etu=<?= htmlspecialchars($lit['id_etu']); ?>&id_lit=<?= htmlspecialchars($lit['id_lit']); ?>&numero=<?= htmlspecialchars($lit['num_etu']); ?>&id_aff=<?= htmlspecialchars($lit['id_aff']); ?>">
                                    traiter
                                </a>
                            </td>

                        </tr>

                        <!-- ================= SUPPLÉANT ================= -->
                        <?php if (!empty($lit['suppleant'])) : ?>
                        <tr style="background-color:#fafafa;">
                            <td style="text-align:center;">—</td>
                            <td><?= htmlspecialchars($lit['id_lit']); ?></td>
                            <td><?= htmlspecialchars($lit['lit']); ?></td>
                            <td><?= htmlspecialchars($lit['niveauFormation']); ?></td>
                            <td>
                                <?= htmlspecialchars($lit['suppleant']['prenoms'] . ' ' . $lit['suppleant']['nom']); ?>
                            </td>
                            <td><?= htmlspecialchars($lit['suppleant']['num_etu']); ?></td>
                            <td><?= htmlspecialchars($lit['suppleant']['sexe']); ?></td>
                            <td><?= htmlspecialchars($lit['suppleant']['telephone']); ?></td>
                            <td><?= htmlspecialchars($lit['suppleant']['statut']); ?></td>
                            <td style="text-align:center;color:gray;">
                                —
                            </td>
                        </tr>
                        <?php endif; ?>

                        <?php endforeach; ?>

                        <?php else : ?>
                        <tr>
                            <td colspan="9" style="text-align:center;">
                                ❌ Aucun résultat.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </center>
    </section>
    <br><br>
    <center>
        <a href="javascript:history.back()" id="retour">Retour</a><br><br>
    </center>
</body>
<?php 
//var_dump($lits);
?>
</html>