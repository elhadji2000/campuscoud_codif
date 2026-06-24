<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
// if (empty($_SESSION['classe'])) {
//     header('location: /campuscoud.com/profils/personnels/niveau.php');
//     exit();
// }
// connexion à la base de données
include ('../../traitement/fonction.php');
connexionBD();
// Sélectionnez les options à partir de la base de données avec une pagination
include ('../../traitement/requete.php');

verif_type_mdp_2($_SESSION['username']);

// Comptez le nombre total d'options dans la base de données details lits affecter (quotas)

$countIn = 0;
if (isset($_GET['erreurValider'])) {
    $_SESSION['erreurValider'] = $_GET['erreurValider'];
} else {
    $_SESSION['erreurValider'] = '';
}
if (isset($_GET['successValider'])) {
    $_SESSION['successValider'] = $_GET['successValider'];
} else {
    $_SESSION['successValider'] = '';
}
if (isset($_GET['erreurNonTrouver'])) {
    $_SESSION['erreurNonTrouver'] = $_GET['erreurNonTrouver'];
} else {
    $_SESSION['erreurNonTrouver'] = '';
}
if (isset($_GET['erreurForclo'])) {
    $_SESSION['erreurForclo'] = $_GET['erreurForclo'];
} else {
    $_SESSION['erreurForclo'] = '';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['rechercher'])) {
        // Récupération des dates
        $date_debut = trim($_POST['date_debut'] ?? '');
        $date_fin = trim($_POST['date_fin'] ?? '');

        // Conservation dans la session
        $_SESSION['debut'] = $date_debut;
        $_SESSION['fin'] = $date_fin;

        $username = $_SESSION['username'];

        // Retourne tous les paiements si les dates sont vides
        $tabPaiment = getPaiementWithDateInterval(
            $date_debut,
            $date_fin,
            $username
        );

        if (empty($tabPaiment)) {
            header('Location: etatPaiement.php?message=' . urlencode('Aucun résultat trouvé'));
            exit();
        } else {
            $queryString = http_build_query(['data' => $tabPaiment]);
            header('Location: etatPaiement.php');
            exit();
        }
    } elseif (isset($_POST['imprimer'])) {
        $dateDebut = urlencode($_POST['date_debut'] ?? '');
        $dateFin = urlencode($_POST['date_fin'] ?? '');

        header("Location: convention/paiementPdf.php?date_debut=$dateDebut&date_fin=$dateFin");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css"
        rel="stylesheet">

    <?php include ('../../head.php'); ?>
    <style>
        table tr th{
            text-align : left !important;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row ">
            <div class="text-center">
                <h1>ETAT DES ENCAISSEMENTS PERIODIQUES</h1><br>
            </div>
        </div>
        <br>

        <!-- Interval date -->
        <form action="" method="POST">
            <div class="col-md-9 d-flex justify-content-center align-items-center">

                <div style="margin: auto; font-size: 16px; font-weight: 400;">
                    <label for="date_debut">Date début :</label>
                    <input type="date" id="date_debut" name="date_debut"
                        value="<?php echo isset($_POST['date_debut']) ? htmlspecialchars($_POST['date_debut']) : ''; ?>" />
                </div>

                <div style="margin: auto; font-size: 16px; font-weight: 400;">
                    <label for="date_fin">Date fin :</label>
                    <input type="date" id="date_fin" name="date_fin"
                        value="<?php echo isset($_POST['date_fin']) ? htmlspecialchars($_POST['date_fin']) : ''; ?>" />
                </div>

                <div style="margin: auto; font-size: 16px; font-weight: 400;">
                    <button name="rechercher" type="submit" class="btn btn-lg btn-success">
                        Rechercher
                    </button>
                </div>

                <div style="margin: auto;">
                    <button name="imprimer" type="submit" class="btn btn-lg btn-info" formtarget="_blank">
                        Imprimer
                    </button>
                </div>

            </div>
        </form><br>

        <!-- table -->
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    <?php
                    $username = $_SESSION['username'];

                    $dateDen = $_SESSION['debut'] ?? '';
                    $dateFen = $_SESSION['fin'] ?? '';

                    if (!empty($dateDen) && !empty($dateFen)) {
                        echo '<strong>Date début : '
                            . date('d-m-Y', strtotime($dateDen))
                            . ' et Date fin : '
                            . date('d-m-Y', strtotime($dateFen))
                            . '</strong>';
                    } else {
                        echo '<strong>Tous les paiements du régisseur</strong>';
                    }

                    $donnees = getPaiementWithDateInterval($dateDen, $dateFen, $username);
                    ?>
                </div>
            </div>
        </div>
        <br>
        <div>
            <table class="table table-hover">
                <tr class="table-secondary" style="font-size: 13px; font-weight: 400;">
                    <th class="text-left">Quittance</th>
                    <th class="text-left">Date</th>
                    <th class="text-left">Libelle</th>
                    <th class="text-left">Num Étudiant</th>
                    <th class="text-left">Prénom et NOM</th>
                    <th>Montant</th>

                </tr>
                <?php $total = 0;
                if (!empty($donnees)): ?>
                <?php foreach ($donnees as $index => $row): ?>
                <tr style="font-size: 12px;">
                    <td class="text-left"><?php echo htmlspecialchars($row['quittance']); ?></td>
                    <td class="text-left"><?php echo htmlspecialchars($row['dateTime_paie']); ?></td>
                    <td class="text-left"><?php echo htmlspecialchars($row['libelle']); ?></td>
                    <td class="text-left"><?php echo htmlspecialchars($row['num_etu']); ?></td>
                    <td class="text-left"><?php echo htmlspecialchars($row['prenoms'] . ' ' . $row['nom']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['montant']); ?></td>

                    <?php $total += $row['montant']; ?>
                </tr>
                <?php endforeach; ?>

                <tr style="font-size: 13px;">
                    <td colspan="5" align="center" style="border: 1px solid #dddddd; text-align: left; padding: 8px;">
                        TOTAL DE LA PERIODE</td>
                    <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">
                        <?php echo htmlspecialchars($total); ?></td>
                </tr>
                <?php else: ?>
                <tr>
                    <?php if (!empty($_GET['data'])) ?>
                    <td colspan="6">Aucun résultat trouvé</td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        <br><br>
    </div>

    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>

    <!-- JavaScript de Bootstrap (assurez-vous d'ajuster le chemin si nécessaire) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
<script src="../../assets/js/script.js"></script>
</body>

</html>