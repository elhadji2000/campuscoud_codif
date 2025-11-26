<?php 
session_start();
include('../../traitement/fonction.php');

verif_type_mdp_2($_SESSION['username']); 

$fac = isset($_GET["fac"]) ? htmlspecialchars($_GET["fac"]) : "F.S.T";
$lits = getTitulaireAndSuppleantByFac($fac);
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
    <div class="container-fluid" style="font-size:16px;">
        <center>
            <br>
            <div class="container" style="width:50%;">
                <form method="get" action="suppleant">
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

            <br>
            <h2>Les Etudiants Suppleant(e) qui n'ont pas encore valider leur Lit.</h2>
            <h3>Faculter : <?= htmlspecialchars($fac) ?></h3>
        </center>

        <br><br>

        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Lit</th>
                    <th>Titulaire</th>
                    <th>Num_Titul</th>
                    <th>Suppléant</th>
                    <th>Num_suppl</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($lits)): ?>

                <?php $counter = 1; foreach ($lits as $row): 
                            $t = $row['titulaire'];
                            $s = $row['suppleant'];
                        ?>
                <tr>
                    <th><?= $counter++ ?></th>
                    <td><?= $row['lit'] ?></td>

                    <td><?= $t['prenom'] . " " . $t['nom'] ?></td>
                    <td><?= $t['num_etu'] ?></td>

                    <?php if ($s): ?>
                    <td><?= $s['prenom'] . " " . $s['nom'] ?></td>
                    <td><?= $s['num_etu'] ?></td>
                    <?php else: ?>
                    <td colspan="2" style="color:red;text-align:center;">Aucun suppléant trouvé</td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6" style="color:red;text-align:center;">Aucun étudiant trouvé pour ce Faculter.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="text-center my-5">
            <button class="btn btn-success" onclick="window.history.back()">Retour</button>
        </div>
    </div>


    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>