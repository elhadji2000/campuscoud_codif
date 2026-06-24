<?php session_start(); ?>
<?php 
//include('head.html');	 
include ('../../traitement/fonction.php');

if (!isset($_SESSION['username'])) {
    header("location: ../../");
    exit();
}

include('../../activite.php'); 

$stats = getStatistiquesToutesFacultes_2();
$statsCampus = getStatsCampus();
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


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        crossorigin="anonymous" />
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- Buttons (Export Excel) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
</head>
<?php include('../../head.php'); ?>
<style>
/* Réduire la taille globale du DataTable */
.dataTables_wrapper {
    font-size: 13px;
}

/* Champ de recherche */
.dataTables_filter input {
    font-size: 12px;
    padding: 3px 6px;
    height: 28px;
}

/* Texte "Rechercher" */
.dataTables_filter label {
    font-size: 12px;
}

/* Select "Afficher X lignes" */
.dataTables_length select {
    font-size: 12px;
    padding: 2px 5px;
    height: 28px;
}

/* Texte "Afficher X lignes" */
.dataTables_length label {
    font-size: 12px;
}

/* Pagination */
.dataTables_paginate {
    font-size: 12px;
}

/* Infos (Affichage de X à Y...) */
.dataTables_info {
    font-size: 12px;
}

/* Tableau lui-même */
#tableStats {
    font-size: 13px;
}

/* Header tableau */
#tableStats thead th {
    font-size: 13px;
}

/* Cellules */
#tableStats tbody td {
    font-size: 12.5px;
}

.container {
    max-width: 800px;
    margin: 10px auto;
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

.na-item {
    width: 160px;           /* même largeur */
    text-align: center;     /* texte centré */
    font-size: 14px;        /* taille uniforme */
    font-weight: 500;
    padding: 8px 12px;
    border-radius: 6px;
}

/* effet hover propre */
.na-item:hover {
    transform: translateY(-1px);
    transition: 0.2s;
}
</style>

<div class="container-fluid mt-4">

    
    <div class="mb-3 d-flex gap-2">

    <button class="btn btn-primary nav-btn active na-item" data-target="faculte">
        Facultés
    </button>

    <button class="btn btn-outline-primary nav-btn na-item" data-target="capacite">
        Capacité
    </button>

</div>
    <div id="faculte" class="content-section">
        <h4 class="mb-3"> Statistiques par faculté</h4>
        <table id="tableStats" class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>Faculté</th>
                    <th>Nb Chambres</th>
                    <th>Nb Lits</th>
                    <th>G Affectés</th>
                    <th>F Affectées</th>
                    <th>G Validés</th>
                    <th>F Validées</th>
                    <th>G Logés</th>
                    <th>F Logées</th>
                </tr>
            </thead>

            <tbody>
                <?php 
        $tot = ['ch'=>0,'lits'=>0,'ag'=>0,'af'=>0,'vg'=>0,'vf'=>0,'lg'=>0,'lf'=>0];

        foreach ($stats as $s): 

            $tot['ch'] += $s['nb_chambres'];
            $tot['lits'] += $s['nb_lits'];
            $tot['ag'] += $s['affecte_garcon'];
            $tot['af'] += $s['affecte_fille'];
            $tot['vg'] += $s['valide_garcon'];
            $tot['vf'] += $s['valide_fille'];
            $tot['lg'] += $s['loge_garcon'];
            $tot['lf'] += $s['loge_fille'];
        ?>
                <tr>
                    <td><?= htmlspecialchars($s['etablissement']) ?></td>
                    <td><?= $s['nb_chambres'] ?></td>
                    <td><?= $s['nb_lits'] ?></td>
                    <td><?= $s['affecte_garcon'] ?></td>
                    <td><?= $s['affecte_fille'] ?></td>
                    <td><?= $s['valide_garcon'] ?></td>
                    <td><?= $s['valide_fille'] ?></td>
                    <td><?= $s['loge_garcon'] ?></td>
                    <td><?= $s['loge_fille'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>

            <tfoot class="table-success">
                <tr>
                    <th>TOTAL</th>
                    <th><?= $tot['ch'] ?></th>
                    <th><?= $tot['lits'] ?></th>
                    <th><?= $tot['ag'] ?></th>
                    <th><?= $tot['af'] ?></th>
                    <th><?= $tot['vg'] ?></th>
                    <th><?= $tot['vf'] ?></th>
                    <th><?= $tot['lg'] ?></th>
                    <th><?= $tot['lf'] ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div id="capacite" class="content-section" style="display:none;">

        <h4 class="mb-3">Capacité par campus</h4>

        <table id="tableCampus" class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>Campus</th>
                    <th>Pavillons</th>
                    <th>Chambres</th>
                    <th>Lits</th>
                    <th>Garçons</th>
                    <th>Filles</th>
                </tr>
            </thead>

            <tbody>
                <?php 
            $tot = ['pav'=>0,'ch'=>0,'lit'=>0,'g'=>0,'f'=>0];

            foreach ($statsCampus as $c):

                $tot['pav'] += $c['nb_pavillons'];
                $tot['ch'] += $c['nb_chambres'];
                $tot['lit'] += $c['nb_lits'];
                $tot['g'] += $c['lits_garcons'];
                $tot['f'] += $c['lits_filles'];
            ?>
                <tr>
                    <td><?= htmlspecialchars($c['campus']) ?></td>
                    <td><?= $c['nb_pavillons'] ?></td>
                    <td><?= $c['nb_chambres'] ?></td>
                    <td><?= $c['nb_lits'] ?></td>
                    <td><?= $c['lits_garcons'] ?></td>
                    <td><?= $c['lits_filles'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>

            <tfoot class="table-primary">
                <tr>
                    <th>TOTAL</th>
                    <th><?= $tot['pav'] ?></th>
                    <th><?= $tot['ch'] ?></th>
                    <th><?= $tot['lit'] ?></th>
                    <th><?= $tot['g'] ?></th>
                    <th><?= $tot['f'] ?></th>
                </tr>
            </tfoot>
        </table>

    </div>

</div>


<script>
$(document).ready(function() {

    // NAVIGATION
    $('.nav-btn').click(function() {
        let target = $(this).data('target');

        $('.nav-btn').removeClass('btn-primary active')
            .addClass('btn-outline-primary');

        $(this).removeClass('btn-outline-primary')
            .addClass('btn-primary active');

        $('.content-section').hide();
        $('#' + target).fadeIn();
    });

    // DataTable Faculté
    $('#tableStats').DataTable({
        pageLength: 100,
        dom: 'Bfrtip',
        buttons: ['excel']
    });

    // DataTable Campus
    $('#tableCampus').DataTable({
        pageLength: 10,
        dom: 'Bfrtip',
        buttons: ['excel']
    });

});
</script>