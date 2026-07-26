<?php
session_start();

// Vérification de l'authentification
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}

require_once('../../traitement/fonction.php');
verif_type_mdp($_SESSION['username']);

// Récupération des données de paiement
$paiement = $_SESSION['orange'] ?? [];

if(empty($paiement)){
    $_SESSION['error'] = "Aucun paiement Orange Money trouvé.";
    header("Location: payer.php");
    exit();
}

$qrCode     = $paiement['qrCode'] ?? '';
$deepLink   = $paiement['deepLink'] ?? '';
$montant    = $paiement['montant'] ?? 0;
$reference  = $paiement['reference'] ?? '';
$validity   = $paiement['validity'] ?? 0;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>COUD: Paiement Logement</title>

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
    /* --- STYLES MINIMALISTES ET PROFESSIONNELS --- */
    body {
        background: #f5f7fa;
        font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        padding: 20px 0;
    }

    .card-paiement {
        max-width: 400px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        border: none;
    }

    /* En-tête */
    .card-header-custom {
        background: #ff7900;
        padding: 20px 15px;
        text-align: center;
        color: white;
    }

    .card-header-custom i {
        font-size: 32px;
    }

    .card-header-custom h2 {
        font-size: 20px;
        font-weight: 700;
        margin: 6px 0 0;
        letter-spacing: -0.3px;
    }

    .card-header-custom small {
        opacity: 0.9;
        font-size: 13px;
    }

    /* Corps */
    .card-body-custom {
        padding: 24px 20px 20px;
    }

    .montant {
        font-size: 34px;
        font-weight: 700;
        color: #ff7900;
        text-align: center;
        margin-bottom: 20px;
        letter-spacing: -0.5px;
    }

    .montant small {
        font-size: 16px;
        font-weight: 400;
        color: #6c757d;
    }

    .qr-box {
        max-width: 220px;
        margin: 10px auto 18px;
        padding: 12px;
        background: #fafbfc;
        border-radius: 18px;
        border: 1px solid #eaedf0;
    }

    .qr-box img {
        width: 100%;
        height: auto;
        display: block;
    }

    .info-text {
        background: #f8f9fc;
        padding: 14px 12px;
        border-radius: 16px;
        font-size: 14px;
        color: #2d3e50;
        text-align: center;
        margin: 12px 0 18px;
        border: 1px solid #e9edf2;
    }

    .info-text i {
        color: #ff7900;
        margin-right: 6px;
    }

    .btn-orange {
        background: #ff7900;
        border: none;
        color: white;
        font-weight: 600;
        padding: 14px;
        border-radius: 40px;
        width: 100%;
        transition: 0.15s;
        font-size: 16px;
    }

    .btn-orange:hover {
        background: #e66d00;
        color: white;
    }

    .btn-outline-copy {
        border: 1px solid #d0d7dd;
        color: #2d3e50;
        background: white;
        font-weight: 500;
        padding: 12px;
        border-radius: 40px;
        width: 100%;
        transition: 0.15s;
        font-size: 15px;
        margin-top: 10px;
    }

    .btn-outline-copy:hover {
        background: #f2f5f8;
        border-color: #b0b9c2;
    }

    .badge-ref {
        background: #f2f5f8;
        padding: 12px 14px;
        border-radius: 40px;
        font-size: 14px;
        color: #1d2b36;
        text-align: center;
        margin-top: 18px;
        border: 1px solid #e2e8ef;
    }

    .badge-ref i {
        color: #ff7900;
        width: 22px;
    }

    .badge-ref span {
        font-weight: 600;
    }

    .footer-card {
        padding: 14px;
        text-align: center;
        font-size: 13px;
        color: #7e8a98;
        border-top: 1px solid #edf2f6;
        background: #fafcfe;
    }

    .footer-card i {
        color: #28a745;
        margin-right: 5px;
    }

    /* Ajustements pour très petits écrans */
    @media (max-width: 400px) {
        .card-header-custom h2 {
            font-size: 18px;
        }

        .montant {
            font-size: 28px;
        }

        .qr-box {
            max-width: 180px;
        }

        .btn-orange,
        .btn-outline-copy {
            font-size: 15px;
            padding: 12px;
        }
    }
    </style>
</head>

<body>

    <!-- Inclusion du head (menu) -->
    <?php include('../../head.php'); ?>

    <div class="container px-3">
        <div class="card-paiement">

            <!-- EN-TÊTE -->
            <div class="card-header-custom">
                <i class="fa-solid fa-qrcode"></i>
                <h2>Orange Money</h2>
                <small>Paiement sécurisé</small>
            </div>

            <!-- CORPS -->
            <div class="card-body-custom">

                <!-- MONTANT -->
                <div class="montant">
                    <?= number_format($montant, 0, ' ', ' ') ?> <small>FCFA</small>
                </div>

                <!-- QR CODE -->
                <?php if(!empty($qrCode)): ?>
                <div class="qr-box">
                    <img src="data:image/png;base64,<?= $qrCode ?>" alt="QR Orange Money">
                </div>
                <?php else: ?>
                <div class="alert alert-warning text-center py-2" style="border-radius:40px; font-size:14px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> QR Code indisponible
                </div>
                <?php endif; ?>

                <!-- INFO -->
                <div class="info-text">
                    <i class="fa-regular fa-circle-check"></i>
                    Scannez le QR avec l'application Orange Money
                    <br class="d-sm-none">
                    <span class="d-none d-sm-inline">•</span>
                    ou utilisez le lien ci-dessous.
                </div>

                <!-- BOUTON ORANGE + COPIE -->
                <?php if(!empty($deepLink)): ?>
                <a href="<?= htmlspecialchars($deepLink) ?>" target="_blank"
                    class="btn-orange d-block text-center text-decoration-none">
                    <i class="fa-solid fa-mobile-screen-button me-2"></i> Ouvrir Orange Money
                </a>

                <button onclick="copierLien()" class="btn-outline-copy">
                    <i class="fa-regular fa-copy me-2"></i> Copier le lien
                </button>
                <input type="hidden" id="lienPaiement" value="<?= htmlspecialchars($deepLink) ?>">
                <?php endif; ?>

                <!-- RÉFÉRENCE + VALIDITÉ -->
                <!-- <div class="badge-ref">
                    <div class="d-flex justify-content-center flex-wrap gap-3">
                        <span><i class="fa-regular fa-receipt"></i> Réf :
                            <span><?= htmlspecialchars($reference) ?></span></span>
                        <span><i class="fa-regular fa-clock"></i> <span><?= (int)$validity ?>s</span></span>
                    </div>
                </div> -->

            </div>

            <!-- PIED -->
            <div class="footer-card">
                <i class="fa-solid fa-lock"></i> Paiement protégé par Orange Money
            </div>

        </div>
    </div>

    <!-- Script Copie -->
    <script>
    function copierLien() {
        const lien = document.getElementById('lienPaiement').value;
        if (!lien) return;
        navigator.clipboard?.writeText(lien)
            .then(() => alert('Lien Orange Money copié !'))
            .catch(() => {
                // Fallback si clipboard non supporté
                const input = document.createElement('input');
                input.value = lien;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                alert('Lien copié !');
            });
    }
    </script>

    <!-- Bootstrap JS (optionnel pour toasts, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>