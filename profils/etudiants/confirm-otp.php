<?php
session_start();

require_once '../../traitement/kpay_fonction.php';
require_once '../../traitement/fonction.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: payer.php');
    exit();
}

$otp = trim($_POST['otp'] ?? '');

if (empty($otp)) {
    $_SESSION['error'] = 'Veuillez saisir le code OTP.';

    header('Location: otp.php');
    exit();
}

if (
    empty($_SESSION['paiement']) ||
    empty($_SESSION['paiement']['kpayReference']) ||
    empty($_SESSION['paiement']['correlationReference'])
) {
    $_SESSION['error'] = 'Session de paiement expirée.';

    header('Location: payer.php');
    exit();
}
/* var_dump($_SESSION['paiement']);
var_dump($otp);
exit; */

try {
    $telephone = $_SESSION['paiement']['telephone'];
    $montant = $_SESSION['paiement']['montant'];
    $num_etu = $_SESSION['paiement']['num_etu'];

    $kpayReference =
        $_SESSION['paiement']['kpayReference'];

    $correlationReference =
        $_SESSION['paiement']['correlationReference'];

    /*
     * |--------------------------------------------------------------------------
     * | Authentification
     * |--------------------------------------------------------------------------
     */

    $token = $_SESSION['paiement']['token'];

    /*
     * |--------------------------------------------------------------------------
     * | Confirmation OTP
     * |--------------------------------------------------------------------------
     */

    $confirmation = confirmPayment(
        $token,
        $otp,
        $kpayReference,
        $telephone
    );

    if (empty($confirmation)) {
        throw new Exception(
            'Impossible de confirmer le paiement.'
        );
    }

    /*
     * |--------------------------------------------------------------------------
     * | Vérification finale du paiement
     * |--------------------------------------------------------------------------
     */

    sleep(2);

    $verification =
        searchPaymentByCorrelationReference(
            $token,
            $correlationReference
        );

    if (empty($verification)) {
        throw new Exception(
            'Impossible de vérifier le paiement.'
        );
    }

    $status = strtolower(
        $verification['status'] ?? ''
    );

    /*
     * |--------------------------------------------------------------------------
     * | Paiement validé
     * |--------------------------------------------------------------------------
     */

    if ($status === 'succeeded') {
        /*
         * |--------------------------------------------------------------------------
         * | UPDATE paiement
         * |--------------------------------------------------------------------------
         * |
         */

        updatePaiementKpay(
            $kpayReference,
            'SUCCEEDED'
        );

        $_SESSION['success'] =
            'Paiement effectué avec succès.';

        unset($_SESSION['paiement']);

        header('Location: payer.php');
        exit();
    }

    /*
     * |--------------------------------------------------------------------------
     * | Paiement refusé ou en attente
     * |--------------------------------------------------------------------------
     */

    updatePaiementKpay(
        $kpayReference,
        $status
    );

    throw new Exception(
        "Le paiement n'a pas été validé."
    );
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();

    header('Location: otp.php');
    exit();
}
