<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /CCoud/');
    exit();
}

require_once ('../../traitement/fonction.php');

verif_type_mdp($_SESSION['username']);

if (isset($_GET['erreurNum_etu'])) {
    $_SESSION['erreurNum_etu'] = $_GET['erreurNum_etu'];
} else {
    $_SESSION['erreurNum_etu'] = '';
}

if (isset($_GET['data'])) {
    $tableau_data_etudiant = $_GET['data'];
} else {
    $num_etu = $_SESSION['num_etu'];
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: campuscoud</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #f0f2f5;
        font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
    }

    /* Section styling */
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 1.2rem;
        padding-left: 0.5rem;
        border-left: 4px solid;
        text-transform: uppercase;
    }

    .section-title.text-success {
        border-left-color: #10b981;
        color: #064e3b;
    }

    .section-title.text-secondary {
        border-left-color: #94a3b8;
        color: #334155;
    }

    /* GRID - 5 colonnes sur grand écran */
    .grid-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.25rem;
    }

    /* RESPONSIVE */
    @media (max-width: 1200px) {
        .grid-container {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 992px) {
        .grid-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .grid-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .grid-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* CARD RECTANGLE - HAUTEUR AUGMENTÉE */
    .grid-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 1.75rem 1rem;
        min-height: 160px;
        /* Hauteur augmentée */
        height: auto;
        border-radius: 16px;
        text-decoration: none;
        font-weight: 500;
        color: #ffffff;
        background: linear-gradient(135deg, #1e293b, #0f172a);
        transition: all 0.25s ease-in-out;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        cursor: pointer;
    }

    .button {
        text-align: center;
        padding: 1.75rem 1rem;
        height: auto;
        border-radius: 16px;
        text-decoration: none;
        font-weight: 500;
        color: #ffffff;
        cursor: pointer;
    }

    /* Icône plus grande et mieux positionnée */
    .grid-card i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        transition: transform 0.2s ease;
    }

    /* Texte */
    .grid-card span {
        font-size: 0.95rem;
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    /* Hover pro */
    .grid-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 28px rgba(0, 0, 0, 0.12);
        filter: brightness(1.02);
    }

    .grid-card:hover i {
        transform: scale(1.05);
    }
     .button i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        color: #ffffff;
        transition: transform 0.2s ease;
    }

    /* Texte */
    .button span {
        font-size: 0.95rem;
        font-weight: 500;
        color: #ffffff;
        letter-spacing: 0.3px;
    }

    /* Cartes disponibles - couleurs sobres mais distinctes */
    .grid-card:nth-child(1) {
        background: linear-gradient(135deg, #2c3e66, #1a2a4f);
    }

    .grid-card:nth-child(2) {
        background: linear-gradient(135deg, #1e5a5a, #0f3b3b);
    }

    .grid-card:nth-child(3) {
        background: linear-gradient(135deg, #8b5e3c, #6b3e1a);
    }

    .grid-card:nth-child(4) {
        background: linear-gradient(135deg, #413c8b, #13ba9b);
    }

    /* DISABLED (bientôt disponibles) - sobre et professionnel */
    .grid-card.disabled {
        background: #e2e8f0;
        color: #64748b;
        cursor: not-allowed;
        box-shadow: none;
        border: 1px solid #cbd5e1;
        opacity: 0.7;
    }

    .grid-card.disabled i {
        color: #94a3b8;
    }

    .grid-card.disabled:hover {
        transform: none;
        box-shadow: none;
        filter: none;
    }

    .grid-card.disabled:hover i {
        transform: none;
    }

    /* Navbar professionnelle */
    .navbar {
        background: #ffffff !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border-bottom: 1px solid #e9ecef;
    }

    .navbar-brand {
        font-size: 1.25rem;
        letter-spacing: 0.5px;
    }

    .navbar-brand span {
        color: #1e293b;
    }

    .nav-link {
        color: #475569 !important;
        font-weight: 500;
        transition: color 0.2s;
    }

    .nav-link:hover {
        color: #0f172a !important;
    }

    .dropdown-item:active {
        background-color: #f1f5f9;
    }

    /* Container responsive */
    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    /* Petite touche moderne */
    .grid-card span {
        position: relative;
    }

    /* Footer */
    footer {
        margin-top: 3rem;
        padding: 1.5rem 0;
        text-align: center;
        border-top: 1px solid #e2e8f0;
        font-size: 0.8rem;
        color: #64748b;
    }
    section{
        padding : 30px 30px;
        text-align : center;
        background: #3777B0;
        font-size: 20px;
        color: #e5e7ea;
        box-shadow: none;
        border: 1px solid #cbd5e1;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="../../assets/images/logo.png" alt="logo" height="45" width="90" class="me-2"
                    style="object-fit: contain;">
                <span class="fw-bold">CAMPUSCOUD</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="accueil">
                            <i class="fas fa-home me-1"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($_SESSION['profil'] != 'user') { ?>
                            <li><a class="dropdown-item" href="mp"><i class="fas fa-key me-2"></i> Mot de passe</a></li>
                            <?php } ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="../../log">
                                    <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
<section>
    <p><strong>Bienvenue dans votre espace Etudiant !</strong><p>
</section>
    <div class="container mt-4 pt-2">
        <!-- SERVICES DISPONIBLES -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="section-title text-success mb-0">Services disponibles</h4>
            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill small">Actifs</span>
        </div>

        <div class="grid-container">
            <a target="_blank" href="jwtRestaurant.php" class="grid-card">
                <i class="fas fa-utensils"></i>
                <span>Restauration</span>
            </a>
            <a href="resultat" class="grid-card">
                <i class="fas fa-bed"></i>
                <span>Hébergement</span>
            </a>
            <form action=" " class="grid-card" style="display:inline;">
                <input type="hidden" name="numero_carte" value="<?= htmlspecialchars($_SESSION['num_etu']) ?>">

                <button type="submit" class="button" style="border:none; background:none;" onclick="return alert('les aide social non disponnible');">
                    <i class="fas fa-hand-holding-heart"></i><br>
                    <span>Aide sociale</span>
                </button>
            </form>
           <!--  <a href="../../guide?numero_carte=<?= urlencode($_SESSION['num_etu']) ?>" class="grid-card">
                <i class="fas fa-book me-1"></i>
                <span>GUIDE</span>
            </a> -->

        </div>

        <!-- BIENTÔT DISPONIBLES -->
        <h4 class="section-title text-secondary mt-5 pt-2">Bientôt disponibles</h4>
        <div class="grid-container">
             <div class="grid-card disabled">
                <i class="fas fa-book me-1"></i>
                <span>Guide</span>
            </div>
            <div class="grid-card disabled">
                <i class="fas fa-heartbeat"></i>
                <span>Santé</span>
            </div>
            <div class="grid-card disabled">
                <i class="fas fa-futbol"></i>
                <span>Sport</span>
            </div>
            <div class="grid-card disabled">
                <i class="fas fa-theater-masks"></i>
                <span>Culture</span>
            </div>
        </div>
    </div>

    <footer>
        <div class="container text-center">
            <small>© <?= date('Y') ?> COUD - MyCoud • Plateforme administrative</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>