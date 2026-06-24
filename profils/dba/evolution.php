<?php
session_start();
include ('../../traitement/fonction.php');
verif_type_mdp_2($_SESSION['username']);
$fac = isset($_GET['fac']) ? htmlspecialchars($_GET['fac']) : 'E.S.P';
$sexe = isset($_GET['sexe']) ? htmlspecialchars($_GET['sexe']) : 'G';
$result = getStatsByFacAndNiveau($fac, $sexe);
// Exemple d'utilisation
// $quotaF = countAffected_2('F.A.S.E.G L1', 'F'); // quota restant filles
// $quotaG = countAffected_2('F.A.S.E.G L1', 'G'); // quota restant garçons
// var_dump($quotaF, $quotaG);

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
    <script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/base.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <style>
    select.fac {
        width: 250px;
        height: 50px;
        font-size: 16px;
        padding: 5px;
        border-radius: 5px;
    }

    table td {
        font-size: 12px;
    }

    table td span {
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
<?php include ('../../head.php'); ?>

<body>
    <div class="container-fluid" style="font-size:16px;">
        <center>
            <br>
            <div class="container" style="width:70%;">
                <form method="get" action="">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-4">
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
                        <div class="col-4">
                            <select name="sexe" class="fac" required>
                                <option value="">-- Choisir le sexe --</option>
                                <option value="G"
                                    <?= (isset($_GET['sexe']) && $_GET['sexe'] == "G" ? 'selected' : '' )?>>
                                    G
                                </option>
                                <option value="F"
                                    <?= (isset($_GET['sexe']) && $_GET['sexe'] == "F" ? 'selected' : '' )?>>
                                    F
                                </option>
                            </select>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary pavillon"><strong>Rechercher</strong></button>
                        </div>
                    </div>
                </form>
            </div>
    </div>

    <?php if (!empty($result)): ?>

    <div class="container mt-4">

        <!-- ===== TITRE ===== -->
        <h4 class="text-center fw-bold mb-3">
            FACULTE : <?= strtoupper($fac) ?> | SEXE : <?= $sexe ?>
        </h4>

        <div class="card shadow-sm">
            <div class="card-body">

                <table id="tableStats" class="table table-bordered table-striped text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>Niveau</th>
                            <th>Voir</th>
                            <th>Total</th>
                            <th>Quota</th>
                            <th>Affectés</th>
                            <th>Validés</th>
                            <th>Payés</th>
                            <th>Logés (Attr)</th>
                            <th>Logés (Suppl)</th>
                            <th>Total Logés</th>
                            <th>% Logés</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                    $totaux = [
                        'total' => 0,
                        'quota' => 0,
                        'affecte' => 0,
                        'valide' => 0,
                        'paye' => 0,
                        'attr' => 0,
                        'supp' => 0,
                        'loge' => 0
                    ];
                    ?>

                        <?php foreach ($result as $row): 

                        $totaux['total'] += $row['total_etudiants'];
                        $totaux['quota'] += $row['quota'];
                        $totaux['affecte'] += $row['nb_affecte'];
                        $totaux['valide'] += $row['nb_valide'];
                        $totaux['paye'] += $row['nb_paye'];
                        $totaux['attr'] += $row['nb_loge_attributaire'];
                        $totaux['supp'] += $row['nb_loge_suppleant'];
                        $totaux['loge'] += $row['total_loge'];
                    ?>

                        <tr>
                            <td><b><?= $row['niveauFormation'] ?></b></td>
                            <td>
                                <a href="#" class="text-decoration-underline btn-voir"
                                    data-niveau="<?= $row['niveauFormation'] ?>" data-sexe="<?= $sexe ?>"
                                    data-fac="<?= $fac ?>">
                                    Voir
                                </a>
                            </td>
                            <td><?= $row['total_etudiants'] ?></td>
                            <td><?= $row['quota'] ?></td>
                            <td><?= $row['nb_affecte'] ?></td>
                            <td><?= $row['nb_valide'] ?></td>
                            <td><?= $row['nb_paye'] ?></td>
                            <td><?= $row['nb_loge_attributaire'] ?></td>
                            <td><?= $row['nb_loge_suppleant'] ?></td>
                            <td><?= $row['total_loge'] ?></td>
                            <td>
                                <span class="text-sm text-danger">
                                    <?= $row['taux_loge'] ?> %
                                </span>
                            </td>
                        </tr>

                        <?php endforeach; ?>
                    </tbody>

                    <!-- ===== FOOTER TOTAL ===== -->
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="2">TOTAL</td>
                            <td><?= $totaux['total'] ?></td>
                            <td><?= $totaux['quota'] ?></td>
                            <td><?= $totaux['affecte'] ?></td>
                            <td><?= $totaux['valide'] ?></td>
                            <td><?= $totaux['paye'] ?></td>
                            <td><?= $totaux['attr'] ?></td>
                            <td><?= $totaux['supp'] ?></td>
                            <td><?= $totaux['loge'] ?></td>
                            <td>
                                <?php
                            $taux_total = ($totaux['loge'] / max($totaux['total'],1)) * 100;
                            echo round($taux_total,2) . " %";
                            ?>
                            </td>
                        </tr>
                    </tfoot>

                </table>

            </div>
        </div>
    </div>

    <?php else: ?>
    <div class="alert alert-warning text-center mt-4">
        Aucun résultat trouvé
    </div>
    <?php endif; ?>

    <script>
    $(document).ready(function() {
        $('#tableStats').DataTable({
            "pageLength": 10,
            "lengthMenu": [10, 25, 50, 100],
            "ordering": true,
            "searching": true,
            "lengthChange": true, // ✅ IMPORTANT

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

    <div class="modal fade" id="modalEtu" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Liste des étudiants</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div id="loader" class="text-center">
                        <i class="fa fa-spinner fa-spin"></i> Chargement...
                    </div>

                    <div id="contentEtu"></div>

                </div>

            </div>
        </div>
    </div>

    <script>
$(document).on('click', '.btn-voir', function(e) {
    e.preventDefault();

    let niveau = $(this).data('niveau');
    let sexe = $(this).data('sexe');
    let fac = $(this).data('fac');

    // Ajouter le niveau dans le titre
    $('#modalTitle').text("Liste des étudiants - " + niveau);

    $('#modalEtu').modal('show');
    $('#contentEtu').html('');
    $('#loader').show();

    $.ajax({
        url: 'getEtudiantsModal.php',
        type: 'POST',
        data: {
            niveau: niveau,
            sexe: sexe,
            fac: fac
        },
        success: function(response) {
            $('#loader').hide();
            $('#contentEtu').html(response);
        }
    });
});
</script>
</body>
<?php

            /* var_dump($result); */
            ?>