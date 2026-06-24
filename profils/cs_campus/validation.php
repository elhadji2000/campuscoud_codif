<?php
session_start();
include ('../../traitement/fonction.php');

verif_type_mdp_2($_SESSION['username']);

$dateRecherche = $_GET['date'] ?? date('Y-m-d');
$typeRecherche = $_GET['type'] ?? 'validation';
$sexe_agent = $_SESSION['sexe_agent'];
var_dump($sexe_agent);
$resultats = [];

if (!empty($dateRecherche)) {
    if ($typeRecherche == 'validation') {
        $sql = "
        SELECT
            e.num_etu,
            e.prenoms,
            e.nom,
            e.etablissement,
            e.NiveauFormation,
            v.dateTime_val AS date_operation,
            lit.lit
        FROM codif_validation v
        LEFT JOIN codif_affectation a ON a.id_aff = v.id_aff
        LEFT JOIN codif_lit lit ON lit.id_lit = a.id_lit
        INNER JOIN codif_etudiant e
            ON e.id_etu = a.id_etu
        WHERE DATE(v.dateTime_val) = '$dateRecherche' AND e.sexe='$sexe_agent'
        ORDER BY v.dateTime_val DESC
        ";
    }elseif ($typeRecherche == 'loger'){
        $sql = "
        SELECT
            e.num_etu,
            e.prenoms,
            e.nom,
            e.etablissement,
            e.NiveauFormation,
            lg.dateTime_loger AS date_operation,
            lit.lit
        FROM codif_validation v
        LEFT JOIN codif_affectation a ON a.id_aff = v.id_aff
        LEFT JOIN codif_lit lit ON lit.id_lit = a.id_lit
        INNER JOIN codif_etudiant e ON e.id_etu = a.id_etu
        INNER JOIN codif_loger lg ON lg.id_etu=e.id_etu
        WHERE DATE(lg.dateTime_loger) = '$dateRecherche' AND e.sexe='$sexe_agent'
        ORDER BY lg.dateTime_loger DESC
        ";
     }elseif ($typeRecherche == 'paiement'){
        $sql = "
        SELECT
            e.num_etu,
            e.prenoms,
            e.nom,
            e.etablissement,
            e.NiveauFormation,
            paie.dateTime_paie AS date_operation,
            lit.lit
        FROM codif_validation v
        LEFT JOIN codif_affectation a ON a.id_aff = v.id_aff
        LEFT JOIN codif_lit lit ON lit.id_lit = a.id_lit
        INNER JOIN codif_etudiant e ON e.id_etu = a.id_etu
        INNER JOIN codif_paiement paie ON paie.id_val=v.id_val
        WHERE DATE(paie.dateTime_paie) = '$dateRecherche' AND e.sexe='$sexe_agent'
        ORDER BY paie.dateTime_paie DESC
        ";
    }elseif ($typeRecherche == 'choix'){
        $sql = "
        SELECT
            e.num_etu,
            e.prenoms,
            e.nom,
            e.etablissement,
            e.NiveauFormation,
            aff.dateTime_aff AS date_operation,
            lit.lit
        FROM codif_validation v
        LEFT JOIN codif_affectation a ON a.id_aff = v.id_aff
        LEFT JOIN codif_lit lit ON lit.id_lit = a.id_lit
        INNER JOIN codif_etudiant e ON e.id_etu = a.id_etu
        INNER JOIN codif_affectation aff ON aff.id_etu=e.id_etu
        WHERE DATE(aff.dateTime_aff) = '$dateRecherche' AND e.sexe='$sexe_agent'
        ORDER BY aff.dateTime_aff DESC
        ";
    }

    $query = mysqli_query($connexion, $sql);

    while ($row = mysqli_fetch_assoc($query)) {
        $resultats[] = $row;
    }
}
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
    select.pavillon {
        width: 250px;
        height: 50px;
        font-size: 16px;
        padding: 5px;
        border-radius: 5px;
    }
    input.pavillon {
        width: 250px;
        height: 50px;
        font-size: 16px;
        padding: 5px;
        border-radius: 5px;
    }

    #tablePaiements td,
    #tablePaiements th {
        font-size: 12px;
    }

    #tablePaiements td a {
        text-decoration: underline !important;
        font-size: 12px !important;
    }
    </style>
</head>
<?php include ('../../head.php'); ?>

<body>
    <div class="container-fluid" style="font-size:16px;">
        <center>
            <div class="container" style="width:70%;">
                <form method="get" action="validation">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-4">
                            <input class="pavillon form-control" type="date" name="date" value="<?= $dateRecherche ?>" />
                        </div>
                        <div class="col-4">
                            <select class="pavillon" name="type" required>
                                <option value="">Sélectionner</option>
                                <option value="choix">
                                    choix
                                </option>
                                <option value="validation">
                                    validation
                                </option>
                                <option value="paiement">
                                    Paiement
                                </option>
                                <option value="loger">
                                    Loger
                                </option>
                            </select>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary pavillon"><strong>Rechercher</strong></button>
                        </div>
                    </div>
                </form>
            </div>

            <br><br>
            <strong>GESTION <?= strtoupper(htmlspecialchars($typeRecherche)) ?>  , total : <?= count($resultats) ?> </strong>
        </center>

        <br><br>

        <table id="tablePaiements" class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                        <th>#</th>
                        <th>Lit</th>
                        <th>Numéro</th>
                        <th>Prénom(s)</th>
                        <th>Nom</th>
                        <th>NiveauFormation</th>
                        <th>Faculté</th>
                        <th>Date opération</th>
                    </tr>
            </thead>
            <tbody>
                <?php if (!empty($resultats)): ?>

                <?php foreach ($resultats as $i => $row): ?>

                <tr>

                    <td><?= $i + 1 ?></td>

                    <td><?= htmlspecialchars($row['lit']) ?></td>

                    <td><?= htmlspecialchars($row['num_etu']) ?></td>

                    <td><?= htmlspecialchars($row['prenoms']) ?></td>

                    <td><?= htmlspecialchars($row['nom']) ?></td>

                    <td><?= htmlspecialchars($row['NiveauFormation']) ?></td>

                    <td><?= htmlspecialchars($row['etablissement']) ?></td>

                    <td>
                        <?= date('d/m/Y H:i', strtotime($row['date_operation'])) ?>
                    </td>

                </tr>

                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7">Aucun étudiant trouvé pour ce pavillon.</td>
                </tr>
                <?php endif; ?>

            </tbody>
        </table>

        <div class="text-center my-5">
            <button class="btn btn-success" onclick="window.history.back()">Retour</button>
        </div>
    </div>

    <script src="../../assets/js/script.js"></script>
    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>