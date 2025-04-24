<?php session_start();  

include('../../traitement/fonction.php');

verif_type_mdp_2($_SESSION['username']);

$users = getUsers();
$pavillons = getAllPavillons($connexion);
$profiles = getAllProfiles($connexion);
$campus = getAllCampus($connexion);
?>
<?php
// Exemple de récupération des données de l'utilisateur à modifier
$userToEdit = null; // Remplacez ceci par votre logique pour récupérer l'utilisateur à modifier
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Requête pour récupérer les informations de l'utilisateur
    $query = "SELECT * FROM codif_user WHERE id_user = $id";
    $result = mysqli_query($connexion, $query);
    $userToEdit = mysqli_fetch_assoc($result);
}
?>


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
    <link rel="stylesheet" href="../../assets/css/base.css" />
    <link rel="stylesheet" href="../../assets/css/login.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <?php include('../../head.php'); ?>
    <style>
    /* Uniformiser les champs de saisie */
    .form-control,
    .full-width {
        background-color: rgba(50, 115, 220, 0.1);
        /* Couleur de fond uniforme */
        font-size: 16px;
        /* Taille du texte uniforme */
    }


    /* Ajouter une ombre légère lors de la sélection */
    .form-control:focus,
    .full-width:focus {
        border-color: #80bdff;
        outline: none;
        box-shadow: 0 0 5px rgba(128, 189, 255, 0.5);
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
    <div class="container-fluid" style="width:70%;">
        <div class="contact__form1">
            <form method="GET" action="../../traitement/traitement.php">
                <input type="hidden" name="form_name"
                    value=<?= $userToEdit ? 'form_modifier_utilisateur' : 'form_enregistrer_utilisateur' ?>>
                <!-- Champ caché pour stocker l'ID de l'utilisateur en mode modification -->
                <?php if ($userToEdit): ?>
                <input type="hidden" name="id_user" value="<?= htmlspecialchars($userToEdit['id_user']); ?>">
                <?php endif; ?>

                <p>
                    <center>
                        <strong><?= $userToEdit ? 'MODIFIEZ LES CHAMPS DE L’UTILISATEUR' : 'VEUILLEZ RENSEIGNER LES CHAMPS' ?></strong>
                    </center>
                </p>

                <center>
                    <div class="row">
                        <div class="col-6">
                            <label for="prenom" class="form-label">PRÈNOM :</label>
                            <input type="text" required placeholder="Prenom: Diopdiop" name="prenom"
                                class="form-control"
                                value="<?= $userToEdit ? htmlspecialchars($userToEdit['prenom_user']) : ''; ?>" />
                        </div>
                        <div class="col-6">
                            <label for="nom" class="form-label">NOM :</label>
                            <input type="text" required placeholder="Nom: DIOP" name="nom" class="form-control"
                                value="<?= $userToEdit ? htmlspecialchars($userToEdit['nom_user']) : ''; ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="telephone" class="form-label">TÈLÈPHONE :</label>
                            <input type="text" required placeholder="Tel: +221 78 441 34 00" name="telephone"
                                class="form-control"
                                value="<?= $userToEdit ? htmlspecialchars($userToEdit['telephone_user']) : ''; ?>" />
                        </div>

                        <div class="col-4">
                            <label for="username" class="form-label">USERNAME :</label>
                            <input type="text" placeholder="username or matricule" required name="username"
                                class="form-control"
                                value="<?= $userToEdit ? htmlspecialchars($userToEdit['username_user']) : ''; ?>" />
                        </div>
                        <div class="col-2">
                            <label for="sexe" class="form-label">SEXE :</label>
                            <select id="sexe" required name="sexe" class="full-width">
                                <option value=""></option>
                                <option value="G"
                                    <?= $userToEdit && $userToEdit['sexe_user'] === 'G' ? 'selected' : ''; ?>>G
                                </option>
                                <option value="F"
                                    <?= $userToEdit && $userToEdit['sexe_user'] === 'F' ? 'selected' : ''; ?>>F
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row tab-full">
                        <div class="col-4">
                            <label for="prof" class="form-label">PROFILE :</label>
                            <select id="prof" required name="prof" class="full-width">
                                <option value="">Sélectionnez un profile</option>
                                <?php
                                    foreach ($profiles as $profile) {
                                        $selected = $userToEdit && $userToEdit['profil_user'] === $profile ? 'selected' : '';
                                        echo "<option value='" . htmlspecialchars($profile) . "' $selected>" . htmlspecialchars($profile) . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-4 pavillon" style="display: none;">
                            <label for="pav" class="form-label">PAVILLON :</label>
                            <select id="pav" name="pav" class="full-width">
                                <option value="">Sélectionnez un pavillon</option>
                                <?php
                                    foreach ($pavillons as $pavillon) {
                                        $selected = $userToEdit && $userToEdit['pavillon'] === $pavillon ? 'selected' : '';
                                        echo "<option value='" . htmlspecialchars($pavillon) . "' $selected>" . htmlspecialchars($pavillon) . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-4 campus" style="display: none;">
                            <label for="pav" class="form-label">CAMPUS :</label>
                            <select id="pav" name="campus" class="full-width">
                                <option value="">Sélectionnez un Campus</option>
                                <?php
                                    foreach ($campus as $camp) {
                                        $selected = $userToEdit && $userToEdit['campus'] === $camp ? 'selected' : '';
                                        echo "<option value='" . htmlspecialchars($camp) . "' $selected>" . htmlspecialchars($camp) . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-4 var" style="display: none;">
                            <label for="var" class="form-label">VAR :</label>
                            <input type="text" name="var" class="form-control full-width"
                                value="<?= $userToEdit ? htmlspecialchars($userToEdit['var']) : ''; ?>" />
                        </div>
                    </div>
                    <div class="form-field">
                    <button type="submit" class="btn--primary">
                        <strong><?= $userToEdit ? 'MODIFIER' : 'ENREGISTRER' ?></strong>
                    </button>
                    <br><br>
                    <center><a href="index.php">Retour</a></center>
                </div>
                </center>

                
            </form>

        </div>
    </div>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const profileSelect = document.getElementById("prof");
        const pavillonDiv = document.querySelector(".pavillon");
        const varDiv = document.querySelector(".var");
        const varCamp = document.querySelector(".campus");

        // Fonction pour afficher/masquer le pavillon
        const togglePavillonVisibility = () => {
            if (profileSelect.value === "chef_residence") {
                pavillonDiv.style.display = "block"; // Afficher si chef_residence
            } else {
                pavillonDiv.style.display = "none"; // Cacher sinon
            }
            if (profileSelect.value === "paiement") {
                varDiv.style.display = "block"; // Afficher si paiement
            } else {
                varDiv.style.display = "none";
            }
            if (profileSelect.value === "chef_campus") {
                varCamp.style.display = "block"; // Afficher si paiement
            } else {
                varCamp.style.display = "none";
            }
        };

        // Ajouter un écouteur d'événement sur le changement de profil
        profileSelect.addEventListener("change", togglePavillonVisibility);

        // Appeler la fonction lors du chargement initial pour gérer la pré-sélection
        togglePavillonVisibility();
    });
    </script>
    <!-- footer
    ================================================== -->
    <footer>
        <div class="row">
            <div class="col-full">

                <div class="footer-logo">
                    <a class="footer-site-logo" href="#0"><img src="../../assets/images/logo.png"
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


    <?php //include('footer.php'); ?>
    <script src="../../assets/js/script.js"></script>
    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>

</html>