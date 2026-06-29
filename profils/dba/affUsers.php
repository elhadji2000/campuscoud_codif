<?php
session_start();

include ('../../traitement/fonction.php');

verif_type_mdp_2($_SESSION['username']);

// $pavillonDonne = $_SESSION['pavillon'];
$users = getUsers();
if (isset($_GET['action']) && $_GET['action'] == 'resetPassword' && isset($_GET['id'])) {
    $id_user = intval($_GET['id']);

    if (resetPasswordUser($id_user)) {
        echo "<script>
                alert('Mot de passe réinitialisé avec succès.');
                window.location='affUsers';
              </script>";
    } else {
        echo "<script>
                alert('Erreur lors de la réinitialisation du mot de passe.');
                window.location='affUsers';
              </script>";
    }
}

// include('../../head.php');
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
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="../../assets/css/base.css" />
    <link rel="stylesheet" href="../../assets/css/login.css" />
    <!-- Bootstrap -->
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.min.css">

    <?php include ('../../head.php'); ?>
    <style>
    td,
    th,
    tr {
        font-size: 12px !important;
        text-align: center;
        vertical-align: middle;
    }

    td a {
        font-size: 12px !important;
        text-decoration: underline !important;
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
        <center>
            <table id="tableUsers" class="table table-bordered table-striped table-hover">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Prenom</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Téléphone</th>
                        <th scope="col">Sexe</th>
                        <th scope="col">Utilisateur</th>
                        <th scope="col">Rôle</th>
                        <th scope="col">Pavillon</th>
                        <th scope="col">Campus</th>
                        <th scope="col">Type_mdp</th>
                        <th scope="col">Activer/Dèsactiver</th>
                        <th scope="col">Modifier</th>
                        <th scope="col">Réinitialiser</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                    <?php foreach ($users as $index => $user): ?>
                    <tr>
                        <th scope="row"><?= $index + 1 ?></th>
                        <td><?= htmlspecialchars($user['prenom_user'] ?? 'NULL') ?></td>
                        <td><?= htmlspecialchars($user['nom_user'] ?? 'NULL') ?></td>
                        <td><?= htmlspecialchars($user['telephone_user'] ?? 'NULL') ?></td>
                        <td><?= htmlspecialchars($user['sexe_user'] ?? 'NULL') ?></td>
                        <td><?= htmlspecialchars($user['username_user'] ?? 'NULL') ?></td>
                        <td><?= htmlspecialchars($user['profil_user']) ?></td>
                        <td><?= !empty($user['pavillon']) ? htmlspecialchars($user['pavillon']) : 'NULL' ?></td>
                        <td><?= !empty($user['campus']) ? htmlspecialchars($user['campus']) : 'NULL' ?></td>
                        <td><?= htmlspecialchars($user['type_mdp'] ?? 'NULL') ?></td>
                        <!-- Bouton pour supprimer -->
                        <td>
                            <a style="font-size: 3rem;"
                                href="?action=toggleActive&id=<?= urlencode($user['id_user']) ?>&isActive=<?= $user['is_active'] ? 0 : 1 ?>"
                                class="<?= $user['is_active'] ? 'text-success' : 'text-danger' ?>"
                                onclick="return confirm('Êtes-vous sûr de vouloir <?= $user['is_active'] ? 'désactiver' : 'activer' ?> cet utilisateur ?');">
                                <?= $user['is_active'] ? 'activer' : 'dèsactiver' ?>
                            </a>
                        </td>
                        <td>
                            <a href="addUser.php?id=<?= urlencode($user['id_user']) ?>">
                                Modifier
                            </a>
                        </td>
                        <td>
                            <a href="?action=resetPassword&id=<?= $user['id_user'] ?>" class="text-danger"
                                onclick="return confirm('Réinitialiser le mot de passe à COUD ?');">
                                Réinitialiser
                            </a>

                        </td>

                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <br><br>
            <br><br>
            <button class="btn btn-success" onclick="goBack()">Retour</button>

            <script>
            function goBack() {
                window.history.back();
            }
            </script>
        </center>
    </div>
    <!-- footer
    ================================================== -->
    <footer>
        <div class="row">
            <div class="col-full">

                <div class="footer-logo">
                    <a class="footer-site-logo" href="#0"><img src="/campuscoud.com/assets/images/logo.png"
                            alt="Homepage"></a>
                </div>



            </div>
        </div>

        <div class="row footer-bottom">

            <div class="col-twelve">
                <div class="copyright">
                    <span>&copy;Copyright Centre des Oeuvres universitaires de Dakar</span>
                </div>

                <div class="go-top">
                    <a class="smoothscroll" title="Back to Top" href="#top"><i class="im im-arrow-up"
                            aria-hidden="true"></i></a>
                </div>
            </div>

        </div> <!-- end footer-bottom -->

    </footer> <!-- end footer -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <?php

    // Vérifiez si l'action est une activation/désactivation
    if (isset($_GET['action']) && $_GET['action'] === 'toggleActive' && isset($_GET['id']) && isset($_GET['isActive'])) {
        $id_user = intval($_GET['id']);  // Sécurisation de l'ID
        $newStatus = intval($_GET['isActive']);  // Nouveau statut (0 ou 1)

        // Appeler la fonction de mise à jour
        $resultat = mettreAJourStatutUtilisateur($connexion_user, $id_user, $newStatus);

        if ($resultat === true) {
            echo "<script>alert('Statut de l\'utilisateur mis à jour avec succès.');</script>";
            // Redirection pour éviter la répétition de l'action
            echo "<script>window.location.href='?reussie';</script>";
        } else {
            echo "<script>alert('Erreur : " . $resultat . "');</script>";
        }
    }
    ?>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <!-- Export Excel -->
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>

    <script>
    $(function() {

        console.log("jQuery :", $.fn.jquery);

        $('#tableUsers').DataTable({
            pageLength: 50,
            order: [
                [0, 'desc']
            ],
            scrollX: true,
            dom: 'Bfrtip',

            buttons: [{
                extend: 'excelHtml5',
                text: 'Exporter Excel'
            }],

            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json'
            }
        });

    });
    </script>
</body>

</html>