<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /mycoud/');
    exit();
}

require_once ('../../traitement/fonction.php');
verif_type_mdp($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: MyCoud </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
    html,
    body {
        height: 100%;
        margin: 0;
    }

    body {
        background: linear-gradient(135deg, #66b5ea, #4b59a2);
        color: white;
        display: flex;
        flex-direction: column;
    }

    /* NAVBAR fixe en haut */
    .navbar {
        flex-shrink: 0;
    }

    /* CONTENU PRINCIPAL */
    .main-content {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* HERO compact */
    .hero {
        text-align: center;
        margin-bottom: 10px;
    }

    .hero h1 {
        font-size: 1.8rem;
        margin-bottom: 5px;
    }

    .hero p {
        font-size: 0.95rem;
        margin-bottom: 10px;
    }

    /* CARD compacte */
    .download-card {
        background: white;
        color: #333;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    /* boutons */
    .btn-store {
        border-radius: 40px;
        padding: 10px;
        font-size: 14px;
        margin: 6px 0;
    }

    /* icône réduite */
    .icon-big {
        font-size: 40px;
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">

            <!-- LOGO -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="../../assets/images/logo.png" alt="logo" height="50" width="100" class="me-2">
                <span class="fw-bold">MYCOUD</span>
            </a>

            <!-- TOGGLE MOBILE -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- MENU -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="accueil">
                            <i class="fas fa-home"></i> Accueil
                        </a>
                    </li>
                    <!-- DROPDOWN USER -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['username']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($_SESSION['profil'] != 'user') { ?>
                            <li><a class="dropdown-item" href="mp"><i class="fas fa-key"></i> Mot de passe</a></li>
                            <?php } ?>
                            <li>
                                <a class="dropdown-item text-danger" href="../../log">
                                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    <!-- HERO -->
    <div class="main-content">
        <div class="container">

            <!-- HERO -->
            <div class="hero">
                <i class="fas fa-utensils icon-big"></i>
                <h1>Restauration Universitaire</h1>
                <p>Achetz vos ticket depuis votre téléphone 📱</p>
            </div>

            <!-- CARD -->
            <div class="row justify-content-center">
                <div class="col-md-5 col-12">
                    <div class="download-card">

                        <h5 class="mb-2">Télécharger l'application</h5>

                        <a target="_blank" href="https://play.google.com/store/apps/details?id=sn.kpay" class="btn btn-dark btn-store w-100">
                            <i class="fab fa-google-play"></i> Play Store
                        </a>

                        <a target="_blank" href="https://apps.apple.com/sn/app/kpay-transfert-dargent/id6447788738?l=fr-FR" class="btn btn-outline-dark btn-store w-100">
                            <i class="fab fa-apple"></i> App Store
                        </a>

                        <hr>

                        <a href="accueil" class="btn btn-light btn-sm mt-2">
                            ← Retour
                        </a>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- RETOUR -->
    <div class="text-center mb-4">
        <a href="accueil" class="btn btn-light">← Retour à l'accueil</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>