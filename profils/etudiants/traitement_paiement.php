<?php
session_start();

require_once '../../traitement/kpay_fonction.php';
require_once '../../traitement/om_fonction.php';
require_once '../../traitement/fonction.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location:payer.php');
    exit();
}

/*
 * |--------------------------------------------------------------------------
 * | Téléphone
 * |--------------------------------------------------------------------------
 * |
 * | On supprime tous les espaces, tirets, parenthèses...
 * | Puis on ajoute l'indicatif 221 si nécessaire.
 * |
 */

$telephone = $_POST['telephone'] ?? '';
$telephone = preg_replace('/\D/', '', $telephone);
// Enlever un éventuel 221 déjà présent
if (substr($telephone, 0, 3) === '221') {
    $telephone = substr($telephone, 3);
}
// Vérifier qu'il reste 9 chiffres
if (strlen($telephone) != 9) {
    $_SESSION['error'] = 'Numéro de téléphone invalide.';
    header('Location:payer.php');
    exit();
}

/*
 * |--------------------------------------------------------------------------
 * | Montant
 * |--------------------------------------------------------------------------
 * |
 * | On enlève les espaces, les séparateurs de milliers
 * | puis on convertit en entier.
 * |
 */
$montant = $_POST['montant'] ?? '';
$montant = preg_replace('/[^\d]/', '', $montant);

$montant = (int) $montant;
$mode_validation = $_POST['mode_validation'] ?? 'OTP';

if (empty($telephone)) {
    $_SESSION['error'] = 'Numéro de téléphone obligatoire';
    header('Location:payer.php');
    exit();
}
$num_etu = $_SESSION['num_etu'];
$wallet = $_POST['wallet'] ?? 'kpay';

if ($wallet == 'kpay') {
    try {
        // Ajouter l'indicatif Sénégal
        $telephone = '221' . $telephone;

        /*
         * |--------------------------------------------------------------------------
         * | Authentification
         * |--------------------------------------------------------------------------
         */
        $token = getKpayToken();

        /*
         * |--------------------------------------------------------------------------
         * | Paiement
         * |--------------------------------------------------------------------------
         */
        $result = initiatePayment(
            $token,
            $telephone,
            $montant,
            $num_etu
        );

        /*
         * |--------------------------------------------------------------------------
         * | Vérification réponse
         * |--------------------------------------------------------------------------
         */
        if (
            empty($result) ||
            !isset($result['kpayReference'])
        ) {
            throw new Exception(
                "Impossible d'initialiser le paiement."
            );
        }

        $kpayReference = $result['kpayReference'];

        $status = $result['status'] ?? '';

        $message = $result['message'] ?? '';
        $correlationReference = $result['correlationReference'] ?? '';

        /*
         * |--------------------------------------------------------------------------
         * | Sauvegarde session
         * |--------------------------------------------------------------------------
         */
        $_SESSION['paiement'] = [
            'telephone' => $telephone,
            'token' => $token,
            'montant' => $montant,
            'mode_validation' => $mode_validation,
            'num_etu' => $num_etu,
            'kpayReference' => $kpayReference,
            'correlationReference' => $correlationReference,
            'status' => $status
        ];

        /*
         * |--------------------------------------------------------------------------
         * | Référence transaction
         * |--------------------------------------------------------------------------
         */
        $_SESSION['transactionId'] = $kpayReference;

        /*
         * |--------------------------------------------------------------------------
         * | Enregistrement base de données
         * |--------------------------------------------------------------------------
         */

        savePaiementTemp(
            $num_etu,
            $telephone,
            $montant,
            $kpayReference,
            'PENDING'
        );

        /*
         * |--------------------------------------------------------------------------
         * | Redirections
         * |--------------------------------------------------------------------------
         */

        if ($mode_validation === 'OTP') {
            header('Location: otp.php');
            exit();
        }

        if ($mode_validation === 'QR') {
            header('Location: qr.php');
            exit();
        }

        if ($mode_validation === 'IN_APP') {
            header('Location: attente-validation.php');
            exit();
        }

        header('Location:payer.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();

        header('Location:payer.php');
        exit();
    }
} elseif ($wallet == 'orange_money') {
    try {
        /*
         * |--------------------------------------------------------------------------
         * | Authentification Orange Money
         * |--------------------------------------------------------------------------
         */

        $token = getOrangeToken();

        /*
         * |--------------------------------------------------------------------------
         * | Référence interne
         * |--------------------------------------------------------------------------
         */

        $reference = 'OM_' . date('YmdHis') . '_' . $num_etu;

        /*
         * |--------------------------------------------------------------------------
         * | Génération du QR Code
         * |--------------------------------------------------------------------------
         */

        $result = generateOrangeQrCode(
            $token,
            $montant,
            $reference
        );

        /*
         * |--------------------------------------------------------------------------
         * | Vérification de la réponse
         * |--------------------------------------------------------------------------
         */

        // var_dump($result);
        // exit;

        if (
            empty($result) ||
            empty($result['qrCode'])
        ) {
            throw new Exception(
                'Impossible de générer le QR Code Orange Money.'
            );
        }

        /*
         * |--------------------------------------------------------------------------
         * | Sauvegarde Session
         * |--------------------------------------------------------------------------
         */

        $_SESSION['orange'] = [
            'telephone' => $telephone,
            'montant' => $montant,
            'num_etu' => $num_etu,
            'reference' => $reference,
            'token' => $token,
            'deepLink' => $result['deepLink'] ?? '',
            'qrCode' => $result['qrCode'],
            'validity' => $result['validity'] ?? 900,
            'metadata' => $result['metadata'] ?? []
        ];

        /*
         * |--------------------------------------------------------------------------
         * | Référence de transaction
         * |--------------------------------------------------------------------------
         */

        $_SESSION['transactionId'] = $reference;

        /*
         * |--------------------------------------------------------------------------
         * | Sauvegarde en base
         * |--------------------------------------------------------------------------
         */

        savePaiementTemp(
            $num_etu,
            $telephone,
            $montant,
            $reference,
            'PENDING'
        );

        /*
         * |--------------------------------------------------------------------------
         * | Redirection vers la page QR
         * |--------------------------------------------------------------------------
         */

        header('Location: orange_qr.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();

        header('Location:payer.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'Moyen de paiement invalide.';
    header('Location:payer.php');
    exit();
}
