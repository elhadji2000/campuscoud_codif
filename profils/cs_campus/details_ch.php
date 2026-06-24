<?php session_start(); ?>
<html lang="fr">
<?php 
include('head.html');	 
include ('../../traitement/fonction.php');

$link = connexionBD();

if (!isset($_SESSION['username'])) {
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


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>
    <!-- CSS================================================== -->
    <link rel="stylesheet" href="../../assets/css/main.css">
    <!-- script================================================== -->
    <script src="../../assets/js/modernizr.js"></script>
    <script src="../../assets/js/pace.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="../../assets/css/base.css" />
    <link rel="stylesheet" href="../../assets/css/login.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <?php include('../../head.php'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        crossorigin="anonymous" />

    <style>
    /* Amélioration des champs de saisie */
    .form-control {
        background-color: rgba(161, 187, 228, 0.1);
        font-size: 16px;
        height: 60px;
    }

    .form-control:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    /* Centrage et style du conteneur */
    .container {
        max-width: 800px;
        margin: 30px auto;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .container-fluid {
        max-width: 100%;
        margin: 30px auto;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Style du tableau */
    table {
        border-radius: 10px;
        overflow: hidden;
    }

    .table th,
    .table td {
        text-align: center;
        vertical-align: middle;
        font-size: 13px;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Message d'erreur */
    .alert {
        font-size: 18px;
        text-align: center;
        padding: 15px;
        margin-top: 20px;
    }

    /* Style des boutons */
    .btn-custom {
        font-size: 14px;
        padding: 10px 15px;
        border-radius: 5px;
        transition: all 0.3s ease-in-out;
    }

    .btn-custom:hover {
        transform: scale(1.05);
    }
    </style>

<body>

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
                    <input type="text" name="ch" class="form-control" placeholder="EX: 35A0" style="padding:3px;">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success mb-3"
                        style="background-color: #676eebff;">Rechercher</button>
                </div>
            </form>

            <div class="table-responsive container-fluid" style="margin-top:20px; width:70%;text-align:center;">
                <table border="1" class="table table-bordered">
                    <thead>
                        <tr style="background-color:#f2f2f2;">
                            <th>#</th>
                            <th>ID</th>
                            <th>Lit</th>
                            <th>NiveauFormation</th>
                            <th>Choix</th>
                            <th>Valider</th>
                            <th>Payer</th>
                            <th>Loger</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($numch)) : ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">
                                🔸 Veuillez entrer un numéro de chambre pour lancer la recherche.
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
                            <td>
                                <?= htmlspecialchars(!empty($lit['dateTime_val']) 
                                    ? date('d/m/Y', strtotime($lit['dateTime_val'])) 
                                    : 'NoN'); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars(!empty($lit['dateTime_paie']) 
                                    ? date('d/m/Y', strtotime($lit['dateTime_paie'])) 
                                    : 'NoN'); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars(!empty($lit['dateTime_loger']) 
                                    ? date('d/m/Y', strtotime($lit['dateTime_loger'])) 
                                    : 'NoN'); ?>
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