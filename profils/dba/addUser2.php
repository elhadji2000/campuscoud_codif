<?php session_start(); 

include('../../traitement/fonction.php');

verif_type_mdp_2($_SESSION['username']);

//$pavillonDonne = $_SESSION['pavillon'];
$users = getUsers();



 //include('../../head.php'); ?>


<!DOCTYPE html>
<html lang="fr">

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

    <?php include('../../head.php'); ?>
    <style>
    td,
    th,
    tr {
        font-size: 15px;
        text-align: center;
        vertical-align: middle;
        height: 50px;
    }
    </style>
</head>

<body>
    <!--header class="s-header">
        <div class="header-logo">
            <a class="site-logo" href="#"><img src="/campuscoud.com/assets/images/logo.png" alt="Homepage" /></a>
            CAMPUSCOUD
        </div>
		
		<?php //if (($_SESSION['profil'] == 'chef_residence')) { ?>
		<nav class="header-nav-wrap">
      <ul class="header-nav">
          <li class="nav-item active">
            <a class="nav-link" href="recouvr" title="Suivi recouvrement">Recouvrement</a>
          </li>
		  <li class="nav-item active">
            <a class="nav-link" href="pavillon" title="Voir occupants">Pavillon</a>
          </li>
		  <li class="nav-item active">
            <a class="nav-link" href="loger" title="Loger etudiant">Loger_un_etudiant</a>
          </li>
		  <li class="nav-item">
          <a class="nav-link" href="/campuscoud.com/" title="Déconnexion"><i class="fa fa-sign-out" aria-hidden="true"></i> Déconnexion</a>
        </li>
		        </ul>
    </nav>
        <?php //} ?>
		
    </header-->
    <!--section id="homedesigne" class="s-homedesigne">
        <p class="lead">Bienvenue dans l'espace de connexion !</p>
    </section-->
    <div class="container-fluid">
        <center>
            <h2> GESTION DES UILISATEURS</h2><br>
        </center>
        <center>
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr class="table-warning">
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
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)) : ?>
                    <?php foreach ($users as $index => $user) : ?>
                    <tr>
                        <th scope="row"><?= $index + 1 ?></th>
                        <td><?= htmlspecialchars($user['prenom_user']) ?></td>
                        <td><?= htmlspecialchars($user['nom_user']) ?></td>
                        <td><?= htmlspecialchars($user['telephone_user']) ?></td>
                        <td><?= htmlspecialchars($user['sexe_user']) ?></td>
                        <td><?= htmlspecialchars($user['username_user']) ?></td>
                        <td><?= htmlspecialchars($user['profil_user']) ?></td>
                        <td><?= !empty($user['pavillon']) ? htmlspecialchars($user['pavillon']) : "NULL" ?></td>
                        <td><?= !empty($user['campus']) ? htmlspecialchars($user['campus']) : "NULL" ?></td>
                        <td><?= htmlspecialchars($user['type_mdp']) ?></td>
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

                    </tr>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <tr>
                        <td colspan="11">Aucun utilisateur trouvé.</td>
                    </tr>
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
                    <a class="footer-site-logo" href="#0"><img src="/campuscoud.com/assets/images/logo.png" alt="Homepage"></a>
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
    $id_user = intval($_GET['id']); // Sécurisation de l'ID
    $newStatus = intval($_GET['isActive']); // Nouveau statut (0 ou 1)

    // Appeler la fonction de mise à jour
    $resultat = mettreAJourStatutUtilisateur($connexion, $id_user, $newStatus);

    if ($resultat === true) {
        echo "<script>alert('Statut de l\'utilisateur mis à jour avec succès.');</script>";
        // Redirection pour éviter la répétition de l'action
        echo "<script>window.location.href='?reussie';</script>";
    } else {
        echo "<script>alert('Erreur : " . $resultat . "');</script>";
    }
}
?>
    <?php //include('footer.php'); ?>
    <script src="../../assets/js/script.js"></script>
    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>

</html>