<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
require_once('../../traitement/fonction.php');

verif_type_mdp($_SESSION['username']);
ancien_eligible_2($_SESSION['username']);

$num_etu = $_SESSION['num_etu'];
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
        /* ===== STYLES MOBILE-FIRST ===== */
        * {
            box-sizing: border-box;
        }

        body {
            background: #f0f4f8;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding-bottom: 20px;
        }

        /* Réduction des espacements sur mobile */
        .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }

        /* En-tête compact */
        .page-header h1 {
            font-size: 1.3rem;
            margin: 0;
            padding: 12px 0 4px;
        }
        .page-header p {
            font-size: 0.85rem;
            margin-bottom: 10px;
        }

        /* Cartes sans ombre lourde et bordures fines */
        .card {
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            margin-bottom: 16px;
        }
        .card-body {
            padding: 16px !important;
        }
        .card-header {
            padding: 14px 16px;
            border-radius: 16px 16px 0 0 !important;
        }
        .card-header h2 {
            font-size: 1.1rem;
        }

        /* Moyens de paiement : grille 2 colonnes compacte */
        .payment-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .payment-option {
            border: 2px solid #e9edf2;
            border-radius: 14px;
            padding: 14px 6px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: #fff;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .payment-option.selected {
            border-color: #198754;
            background: #f0faf5;
        }
        .payment-option input[type="radio"] {
            display: none;
        }
        .payment-option img {
            max-height: 50px;
            width: auto;
            margin-bottom: 6px;
        }
        .payment-option h6 {
            font-size: 0.8rem;
            margin: 4px 0 0;
            font-weight: 600;
        }
        .payment-option small {
            font-size: 0.65rem;
            color: #888;
        }

        /* Formulaire : champs simples et grands */
        .form-control, .form-select {
            height: 48px;
            border-radius: 10px;
            font-size: 0.95rem;
            border: 1.5px solid #dee2e6;
            background: #fff;
            padding: 0 14px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13,110,253,0.15);
        }

        /* Label + input : groupe compact */
        .input-group-custom {
            margin-bottom: 14px;
        }
        .input-group-custom label {
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 4px;
            display: block;
            color: #1e293b;
        }
        .input-group-custom label i {
            width: 20px;
            color: #0d6efd;
        }

        /* Préfixe téléphone +221 intégré */
        .phone-group {
            display: flex;
            gap: 6px;
        }
        .phone-group .prefix {
            background: #eef2f6;
            border: 1.5px solid #dee2e6;
            border-radius: 10px;
            padding: 0 12px;
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 0.95rem;
            color: #1e293b;
            white-space: nowrap;
            height: 48px;
            flex-shrink: 0;
        }
        .phone-group .form-control {
            flex: 1;
        }

        /* Bouton paiement */
        .btn-payer {
            height: 52px;
            font-size: 1rem;
            border-radius: 12px;
            font-weight: 700;
            background: #198754;
            border: none;
            width: 100%;
            margin-top: 6px;
        }
        .btn-payer:hover {
            background: #157347;
        }

        /* Alertes compactes */
        .alert {
            padding: 10px 14px;
            font-size: 0.85rem;
            border-radius: 10px;
        }

        /* Sécurité */
        .security-badge {
            font-size: 0.75rem;
            color: #5e6f8d;
            text-align: center;
            padding: 10px 0 4px;
        }
        .security-badge i {
            color: #198754;
        }

        /* ===== DESKTOP ===== */
        @media (min-width: 768px) {
            .container-fluid {
                max-width: 960px;
                margin: 0 auto;
                padding: 0 20px;
            }
            .page-header h1 {
                font-size: 1.8rem;
            }
            .payment-option {
                min-height: 150px;
                padding: 20px 10px;
            }
            .payment-option img {
                max-height: 60px;
            }
            .payment-option h6 {
                font-size: 0.9rem;
            }
            .card-body {
                padding: 28px !important;
            }
            .form-control, .form-select {
                height: 52px;
                font-size: 1rem;
            }
            .btn-payer {
                height: 58px;
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            .payment-option {
                min-height: 100px;
                padding: 10px 4px;
            }
            .payment-option img {
                max-height: 40px;
            }
            .payment-option h6 {
                font-size: 0.7rem;
            }
            .card-header h2 {
                font-size: 0.95rem;
            }
        }
    </style>
</head>

<body style="background:#f0f4f8; min-height:100vh;">

    <?php include('../../head.php'); ?>

    <div class="container-fluid py-2">

        <!-- En-tête épuré -->
        <div class="page-header text-center">
            <h1 class="fw-bold text-primary">
                <i class="fas fa-door-open me-1"></i> Paiement Logement
            </h1>
            <p class="text-muted">Choisissez votre moyen et payez en toute sécurité</p>
        </div>

        <!-- Messages -->
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger text-center">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success text-center">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        ?>

        <!-- ROW : paiement + formulaire -->
        <div class="row g-3">

            <!-- MOYENS DE PAIEMENT (col-12 sur mobile) -->
            <div class="col-12 col-lg-5">
                <div class="card border-0">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 text-center">
                            <i class="fas fa-wallet text-primary me-1"></i> Moyens
                        </h5>
                        <div class="payment-grid">
                            <!-- KPay -->
                            <label class="payment-option selected">
                                <input type="radio" name="wallet" value="kpay" checked>
                                <img src="images/kpay.webp" alt="KPay" loading="lazy">
                                <h6>KPay</h6>
                                <small>Sécurisé</small>
                            </label>
                            <!-- Wave -->
                            <label class="payment-option">
                                <input type="radio" name="wallet" value="wave">
                                <img src="images/wave.png" alt="Wave" loading="lazy">
                                <h6>Wave</h6>
                                <small>Sécurisé</small>
                            </label>
                            <!-- Orange Money -->
                            <label class="payment-option">
                                <input type="radio" name="wallet" value="orange_money">
                                <img src="images/orange_money.webp" alt="Orange Money" loading="lazy">
                                <h6>Orange Money</h6>
                                <small>Sécurisé</small>
                            </label>
                            <!-- Free Money (optionnel) -->
                            <label class="payment-option">
                                <input type="radio" name="wallet" value="free_money">
                                <img src="images/Yas_logo_2024.svg" alt="Free Money" loading="lazy">
                                <h6>Free Money</h6>
                                <small>Sécurisé</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORMULAIRE (col-12 sur mobile) -->
            <div class="col-12 col-lg-7">
                <div class="card border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h2 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Formulaire</h2>
                    </div>
                    <div class="card-body">

                        <form method="POST" action="traitement_paiement.php">

                            <!-- Téléphone avec préfixe +221 -->
                            <div class="input-group-custom">
                                <label><i class="fas fa-phone"></i> Numéro de téléphone</label>
                                <div class="phone-group">
                                    <span class="prefix">221</span>
                                    <input type="tel" class="form-control" name="telephone" 
                                           placeholder="78 441 34 00" 
                                           pattern="[0-9]{9}" maxlength="9"
                                           title="9 chiffres (ex: 784413400)"
                                           required>
                                </div>
                                <small class="text-muted" style="font-size:0.7rem;">Ex: 78 441 34 00</small>
                            </div>

                            <!-- Montant -->
                            <div class="input-group-custom">
                                <label><i class="fas fa-coins text-success"></i> Montant (FCFA)</label>
                                <input type="number" class="form-control" name="montant" step="100" required>
                            </div>

                            <!-- Mode de validation -->
                            <div class="input-group-custom">
                                <label><i class="fas fa-shield-alt text-warning"></i> Validation</label>
                                <select class="form-select" name="mode_validation">
                                    <option value="OTP">OTP (SMS)</option>
                                    <!-- <option value="QR">QR Code</option>
                                    <option value="IN_APP">In-App</option> -->
                                </select>
                            </div>

                            <!-- Sécurité -->
                            <div class="security-badge">
                                <i class="fas fa-lock me-1"></i> Transactions sécurisées
                            </div>

                            <!-- Bouton -->
                            <button class="btn btn-success btn-payer">
                                <i class="fas fa-credit-card me-2"></i> Payer maintenant
                            </button>

                        </form>

                    </div>
                </div>
            </div>

        </div><!-- /row -->

    </div><!-- /container -->


    <script>
        // Sélection visuelle des moyens de paiement
        /* document.querySelectorAll('.payment-option').forEach(opt => {
            opt.addEventListener('click', function(e) {
                // Ne pas déclencher si on clique sur l'input (évite double)
                if (e.target.tagName === 'INPUT') return;
                document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
                this.classList.add('selected');
                const radio = this.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;
            });
        }); */

        // Validation minimale du montant (côté client)
       /*  document.querySelector('form').addEventListener('submit', function(e) {
            const montant = parseInt(document.querySelector('[name="montant"]').value);
            if (isNaN(montant) || montant < 3000) {
                e.preventDefault();
                alert('Le montant minimum est de 3 000 FCFA.');
            }
        }); */

        // Nettoyer le champ téléphone (chiffres uniquement)
        document.querySelector('[name="telephone"]').addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    </script>

</body>
</html>