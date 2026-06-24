<?php 
session_start();
include('../../traitement/fonction.php');
	

verif_type_mdp_2($_SESSION['username']); 

$fac = isset($_GET["fac"]) ? htmlspecialchars($_GET["fac"]) : "E.S.P";
//$lits = getTitulaireAndSuppleantByFac2($fac);
$result =  getEtudiantsAttributairesNonAffectes($connexion, $fac); 

//var_dump($result);
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

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
                <form method="get" action="forclo">
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
            <h2>Les Etudiants Attributaire non Choisit et/ou Suppleant(e) non valider.</h2>
            <h3>Faculter : <?= htmlspecialchars($fac) ?></h3>

            <form method="post" action="traitement_forclo.php">

                <div class="table-responsive">
                    <table id="tableEtudiants" class="table table-bordered table-striped table-hover">
                        <thead class="table-white">
                            <tr>
                                <th>
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th>N°</th>
                                <th>Numéro étudiant</th>
                                <th>Nom</th>
                                <th>Prénom(s)</th>
                                <th>Sexe</th>
                                <th>Statut</th>
                                <th>Classe</th>

                            </tr>
                        </thead>
                                     <input type="hidden" name="fac" value="<?= $fac; ?>">
                        <tbody>

                            <?php $i = 1; ?>

                            <?php foreach($result as $etu): ?>


                            <tr>
                                <td>
                                    <input type="checkbox" name="etudiants[]" value="<?= $etu['num_etu']; ?>">
                                </td>

                                <td><?= $i++; ?></td>

                                <td><?= htmlspecialchars($etu['num_etu']); ?></td>

                                <td><?= htmlspecialchars($etu['nom']); ?></td>

                                <td><?= htmlspecialchars($etu['prenoms']); ?></td>

                                <td><?= htmlspecialchars($etu['sexe']); ?></td>
                                <td>
                                    <?= htmlspecialchars($etu['statut']); ?>
                                </td>

                                <td><?= htmlspecialchars($etu['niveauFormation']); ?></td>
                            </tr>

                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Voulez-vous vraiment forclore les étudiants sélectionnés ?');">
                        Forclore la sélection
                    </button>
                </div>

            </form>
        </center>
        <div class="text-center my-5">
            <button class="btn btn-success" onclick="window.history.back()">Retour</button>
        </div>
    </div>


    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <script>
    $(document).ready(function() {

        $('#tableEtudiants').DataTable({
            pageLength: 100,
            lengthMenu: [
                [100, 200, 300, 500, -1],
                [100, 200, 300, 500, "Tous"]
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json"
            }
        });

        $('#checkAll').on('click', function() {
            $('input[name="etudiants[]"]').prop(
                'checked',
                this.checked
            );
        });

    });
    </script>
</body>

</html>