<?php
session_start();

include('../../traitement/fonction.php');

if (!isset($_SESSION['username'])) { 
    header("location: ../../");
    exit();
}

$users = getUsers();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <?php include('../../head.php'); ?>

    <style>
    .container-fluid {
        background: #fff;
        margin-top: 20px;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, .1);
    }

    .dataTables_wrapper {
        font-size: 13px;
    }

    #tableUsers td,
    #tableUsers th {
        vertical-align: middle;
    }
    </style>

</head>

<body>

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>
                <i class="fa fa-users"></i>
                Liste des utilisateurs
            </h3>

            <span class="badge bg-primary fs-6">
                Total : <?= count($users) ?>
            </span>
        </div>

        <table id="tableUsers" class="table table-bordered table-striped table-hover">

            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Téléphone</th>
                    <th>Profil</th>
                    <th>Date création</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($users as $i => $u): ?>

                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($u['username_user']??"NULL") ?></td>
                    <td><?= htmlspecialchars($u['nom_user']??"NULL") ?></td>
                    <td><?= htmlspecialchars($u['prenom_user']??"NULL") ?></td>
                    <td><?= htmlspecialchars($u['telephone_user']??"NULL") ?></td>
                    <td><?= htmlspecialchars($u['profil_user']??"NULL") ?></td>
                    <td><?= !empty($u['datesys'])
                        ? date('d/m/Y H:i', strtotime($u['datesys']))
                        : '' ?>
                    </td>
                </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

    <script>
    $(document).ready(function() {

        $('#tableUsers').DataTable({
            pageLength: 100,
            responsive: true,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel',
                text: 'Exporter Excel'
            }],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            }
        });

    });
    </script>

</body>

</html>