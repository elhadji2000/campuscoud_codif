<?php 
session_start();
include('../../traitement/fonction.php');
verif_type_mdp_2($_SESSION['username']); 
include('fonction2.php');
$fac = isset($_GET["fac"]) ? htmlspecialchars($_GET["fac"]) : "E.S.P";
$result = getLitNonAffByFac($fac);
$result2 = getAttributaireAndSuppleantByFac_2($fac);
// Exemple d'utilisation
//$quotaF = countAffected_2('F.A.S.E.G L1', 'F'); // quota restant filles
//$quotaG = countAffected_2('F.A.S.E.G L1', 'G'); // quota restant garçons
//var_dump($quotaF, $quotaG);


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
                <form method="get" action="nonAffect">
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
            <h1>Les Lits libres</h1>
            <h2>Faculter : <?= htmlspecialchars($fac) ?></h2>
        </center>

        <br><br>

        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Chambre</th>
                    <th>Lit</th>
                    <th>Sexe</th>
                    <th>Num_Titul</th>
                    <th>Titulaire</th>
                    <th>Num_Suppl</th>
                    <th>Suppleant(e)</th>
                    <th>NiveauFormation</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($result)): ?>
                <?php $counter = 1; foreach ($result as $resl => $litRow): ?>
                <tr>
                    <th><?= $counter++ ?></th>
                    <td><?= htmlspecialchars($litRow['chambre']) ?></td>
                    <td><?= htmlspecialchars($litRow['lit']) ?></td>
                    <td><?= htmlspecialchars($litRow['sexe']) ?></td>
                    <td><?= htmlspecialchars($litRow['num_etu']??"NULL") ?></td>
                    <td>
                        <?php if (empty($litRow['num_etu'])): ?>
                        NULL
                        <?php else: ?>
                        <?= htmlspecialchars($litRow['prenoms']) ?>
                        <?= htmlspecialchars($litRow['nom']) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($litRow['num_etu']??"NULL") ?></td>
                    <td>
                        <?php if (empty($litRow['num_etu'])): ?>
                        NULL
                        <?php else: ?>
                        <?= htmlspecialchars($litRow['prenoms']) ?>
                        <?= htmlspecialchars($litRow['nom']) ?>
                        <?php endif; ?>
                    </td>

                    <td><?= htmlspecialchars($litRow['niveauQuota'] ?? "") ?></td>
                </tr>
                <?php endforeach; ?>
                <?php $counter = 1; foreach ($result2 as $litRow): ?>
                <tr>
                    <th><?= $counter++ ?></th>

                    <!-- chambre et lit (toujours null ici car pas de lit affecté) -->
                    <td><?= htmlspecialchars($litRow['lit']??"NULL") ?></td>
                    <td><?= htmlspecialchars($litRow['lit']??"NULL") ?></td>
                    <td><?= htmlspecialchars($litRow['titulaire']['sexe']) ?></td>
                    <!-- Titulaire -->
                    <td><?= htmlspecialchars($litRow['titulaire']['num_etu'] ?? "NULL") ?></td>
                    <td>
                        <?php if (!empty($litRow['titulaire'])): ?>
                        <?= htmlspecialchars($litRow['titulaire']['prenoms']) ?>
                        <?= htmlspecialchars($litRow['titulaire']['nom']) ?>
                        (<?= htmlspecialchars($litRow['titulaire']['rang']) ?>)
                        <?php else: ?>
                        NULL
                        <?php endif; ?>
                    </td>

                    <!-- Suppléant -->
                    <td><?= htmlspecialchars($litRow['suppleant']['num_etu'] ?? "NULL") ?></td>
                    <td>
                        <?php if (!empty($litRow['suppleant'])): ?>
                        <?= htmlspecialchars($litRow['suppleant']['prenoms']) ?>
                        <?= htmlspecialchars($litRow['suppleant']['nom']) ?>
                        (<?= htmlspecialchars($litRow['suppleant']['rang']) ?>)
                        <?php else: ?>
                        NULL
                        <?php endif; ?>
                    </td>

                    <!-- Niveau / classe -->
                    <td><?= htmlspecialchars($litRow['titulaire']['classe'] ?? "NaN") ?></td>
                </tr>
                <?php endforeach; ?>

                <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align:center;">Aucun étudiant trouvé pour ce faculter.</td>
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