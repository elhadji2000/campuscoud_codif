<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}

require_once('../../traitement/fonction.php');

verif_type_mdp($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>COUD: Validation OTP</title>

    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/base.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        /* ===== STYLES MOBILE-FIRST ===== */
        * {
            box-sizing: border-box;
        }

        body {
            background: #f0f4f8;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 20px 12px;
        }

        /* Carte épurée */
        .card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            background: #fff;
        }

        .card-header {
            background: #093c8d !important;
            padding: 24px 20px 20px;
            border-radius: 20px 20px 0 0 !important;
            text-align: center;
        }

        .card-header .icon-wrapper {
            width: 64px; 
            height: 64px;
            background: rgba(255,255,255,0.18);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .card-header .icon-wrapper i {
            font-size: 28px;
            color: #fff;
        }

        .card-header h2 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
        }

        .card-header small {
            color: rgba(255,255,255,0.8);
            font-size: 0.8rem;
        }

        .card-body {
            padding: 28px 22px 20px;
        }

        /* Message d'info */
        .info-text {
            text-align: center;
            margin-bottom: 24px;
        }
        .info-text h4 {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .info-text p {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0;
        }

        /* Champ OTP - compact et lisible */
        .otp-group {
            margin-bottom: 20px;
        }
        .otp-group label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #1e293b;
            display: block;
            margin-bottom: 6px;
        }
        .otp-group label i {
            color: #f59e0b;
            margin-right: 6px;
        }

        .otp-input-wrapper {
            position: relative;
        }
        .otp-input-wrapper input {
            width: 100%;
            height: 56px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            text-align: center;
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 12px;
            padding: 0 10px;
            background: #f8fafc;
            transition: border-color 0.25s, box-shadow 0.25s;
            color: #0f172a;
        }
        .otp-input-wrapper input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.12);
            background: #fff;
            outline: none;
        }
        .otp-input-wrapper input::placeholder {
            letter-spacing: 4px;
            font-weight: 400;
            color: #94a3b8;
            font-size: 1.4rem;
        }

        .otp-hint {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 6px;
            display: block;
        }

        /* Alertes compactes */
        .alert {
            padding: 10px 14px;
            font-size: 0.82rem;
            border-radius: 10px;
            margin-bottom: 16px;
        }

        /* Sécurité */
        .security-badge {
            background: #f8fafc;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.78rem;
            color: #475569;
            text-align: center;
            margin-bottom: 18px;
            border: 1px solid #eef2f6;
        }
        .security-badge i {
            color: #198754;
            margin-right: 6px;
        }

        /* Bouton */
        .btn-confirm {
            width: 100%;
            height: 52px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            background: #198754;
            border: none;
            color: #fff;
            transition: background 0.2s;
        }
        .btn-confirm:hover {
            background: #157347;
        }
        .btn-confirm i {
            margin-right: 8px;
        }

        /* Pied de carte */
        .card-footer {
            background: #fff;
            border-top: 1px solid #f1f5f9;
            padding: 14px 20px;
            text-align: center;
            border-radius: 0 0 20px 20px !important;
        }
        .card-footer small {
            font-size: 0.78rem;
            color: #64748b;
        }
        .card-footer a {
            color: #0d6efd;
            font-weight: 600;
            text-decoration: none;
        }
        .card-footer a:hover {
            text-decoration: underline;
        }

        /* ===== RESPONSIVE ===== */
        @media (min-width: 576px) {
            .card-body {
                padding: 36px 32px 28px;
            }
            .otp-input-wrapper input {
                height: 64px;
                font-size: 2rem;
                letter-spacing: 14px;
            }
            .card-header h2 {
                font-size: 1.5rem;
            }
            .btn-confirm {
                height: 56px;
                font-size: 1.05rem;
            }
        }

        @media (max-width: 400px) {
            .card-body {
                padding: 20px 14px 16px;
            }
            .otp-input-wrapper input {
                height: 48px;
                font-size: 1.4rem;
                letter-spacing: 8px;
            }
            .card-header {
                padding: 18px 14px 14px;
            }
            .card-header .icon-wrapper {
                width: 50px;
                height: 50px;
            }
            .card-header .icon-wrapper i {
                font-size: 22px;
            }
            .card-header h2 {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>

    <?php include('../../head.php'); ?>

    <div class="container">
        <div class="card">

            <!-- ===== EN-TÊTE ===== -->
            <div class="card-header">
                <div class="icon-wrapper">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2>Validation OTP</h2>
                <small>Sécurisation de votre paiement</small>
            </div>

            <!-- ===== CORPS ===== -->
            <div class="card-body">

                <!-- Message -->
                <div class="info-text">
                    <h4><i class="fas fa-sms text-primary me-1"></i> Vérification</h4>
                    <p>Un code à 6 chiffres vous a été envoyé par SMS.</p>
                </div>

                <!-- Messages d'erreur / succès -->
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

                <!-- Formulaire -->
                <form action="confirm-otp.php" method="POST">

                    <div class="otp-group">
                        <label><i class="fas fa-key"></i> Code OTP</label>
                        <div class="otp-input-wrapper">
                            <input type="text" name="otp" 
                                   maxlength="6" 
                                   placeholder="• • • • • •"
                                   inputmode="numeric"
                                   pattern="[0-9]{6}"
                                   autocomplete="one-time-code"
                                   required>
                        </div>
                        <span class="otp-hint">Saisissez les 6 chiffres reçus</span>
                    </div>

                    <!-- Sécurité -->
                    <div class="security-badge">
                        <i class="fas fa-lock"></i> Transactions protégées
                    </div>

                    <!-- Bouton -->
                    <button type="submit" class="btn-confirm">
                        <i class="fas fa-check-circle"></i> Confirmer le paiement
                    </button>

                </form>

            </div>

            <!-- ===== PIED ===== -->
            <div class="card-footer">
                <small>
                    Vous n'avez pas reçu le code ?
                    <a href="#" onclick="alert('Un nouveau code vous sera envoyé.'); return false;">Renvoyer</a>
                </small>
            </div>

        </div>
    </div>

    <script>
        // Auto-focus sur le champ OTP
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.querySelector('input[name="otp"]');
            if (otpInput) {
                otpInput.focus();
            }
        });

        // Accepter uniquement les chiffres
        document.querySelector('input[name="otp"]').addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            // Si 6 chiffres, on peut auto-soumettre ? (optionnel)
            // if (this.value.length === 6) {
            //     this.closest('form').submit();
            // }
        });

        // Empêcher les lettres/collage non numérique
        document.querySelector('input[name="otp"]').addEventListener('keydown', function(e) {
            if (e.key === 'e' || e.key === 'E' || e.key === '-' || e.key === '+') {
                e.preventDefault();
            }
        });
    </script>

</body>
</html>