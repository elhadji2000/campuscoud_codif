<?php session_start(); ?>
<html lang="fr">
<?php 
include ('../../traitement/fonction.php');

$link = connexionBD();

verif_type_mdp_2($_SESSION['username']);

include('../../activite.php'); 

if (isset($_GET['etu1'])) {
    $num_etu1 = trim($_GET['etu1']?? "");
    $num_etu2 = trim($_GET['etu2']?? "");
    } else {
    $num_etu1 = null;
    $num_etu2 = null;
}

$lits = getDeuxEtudiantsFull($link, $num_etu1, $num_etu2);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <style>
    input.fac {
        height: 40px;
        font-size: 15px;
        padding: 3px;
        border-radius: 3px;
    }

    table td,
    th {
        font-size: 12px;
    }

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
            <br>
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <label for="search" style="font-weight: bold; font-size: 16px; color: #333;">
                    Rechercher Les 2 Etudiants Pour Permuter !
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

            <div class="container" style="width:55%;">
                <form method="get" action="permuter">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-4">
                            <input type="text" name="etu1" class="fac" placeholder="ETU_1" required
                                value="<?= htmlspecialchars($_GET['etu1'] ?? '') ?>" style="padding:2px;">
                        </div>
                        <div class="col-4">
                            <input type="text" name="etu2" class="fac" placeholder="ETU_2" required
                                value="<?= htmlspecialchars($_GET['etu2'] ?? '') ?>" style="padding:2px;">
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary pavillon"><strong>Rechercher</strong></button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive container-fluid" style="margin-top:20px; width:95%;text-align:center;">
                <table border="1" id="tableStats" class="table table-bordered">
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

                        <!-- ETUDIANT 1 -->
                        <tr>
                            <td>1</td>
                            <td><?= htmlspecialchars($lits[$num_etu1]['id_lit'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($lits[$num_etu1]['lit'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($lits[$num_etu1]['niveauFormation'] ?? '-') ?></td>
                            <td>
                                <?= htmlspecialchars(($lits[$num_etu1]['prenoms'] ?? '') . ' ' . ($lits[$num_etu1]['nom'] ?? '')) ?>
                            </td>
                            <td><?= htmlspecialchars($lits[$num_etu1]['num_etu'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($lits[$num_etu1]['sexe'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($lits[$num_etu1]['telephone'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($lits[$num_etu1]['statut_affectation'] ?? '-') ?></td>
                            <td>
                                <a href="trait_per.php?etu1=<?= $num_etu1 ?>&etu2=<?= $num_etu2 ?>"
                                    onclick="return confirm('Confirmer la permutation ?');">
                                    Permuter
                                </a>
                            </td>
                        </tr>

                        <!-- ETUDIANT 2 -->
                        <tr style="background-color:#f9f9f9;">
                            <td>2</td>
                            <td><?= htmlspecialchars($lits[$num_etu2]['id_lit'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($lits[$num_etu2]['lit'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($lits[$num_etu2]['niveauFormation'] ?? '-') ?></td>
                            <td>
                                <?= htmlspecialchars(($lits[$num_etu2]['prenoms'] ?? '') . ' ' . ($lits[$num_etu2]['nom'] ?? '')) ?>
                            </td>
                            <td><?= htmlspecialchars($lits[$num_etu2]['num_etu'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($lits[$num_etu2]['sexe'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($lits[$num_etu2]['telephone'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($lits[$num_etu2]['statut_affectation'] ?? '-') ?></td>
                            <td>—</td>
                        </tr>

                    </tbody>

                </table>
            </div>
        </center>
    </section>
    <br><br>
    <center>
        <a href="javascript:history.back()" id="retour">Retour</a><br><br>
    </center>

    <script>
    $(document).ready(function() {
        $('#tableStats').DataTable({
            "pageLength": 10,
            "lengthMenu": [10, 25, 50, 100],
            "ordering": true,
            "searching": true,
            "lengthChange": true, //  IMPORTANT

            "language": {
                "search": "Rechercher :",
                "lengthMenu": "Afficher _MENU_ lignes",
                "zeroRecords": "Aucune donnée trouvée",
                "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                "infoEmpty": "Aucune donnée disponible",
                "paginate": {
                    "next": "Suivant",
                    "previous": "Précédent"
                }
            }
        });
    });
    </script>
</body>

</html>