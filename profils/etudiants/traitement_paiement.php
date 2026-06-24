<?php
session_start();

require_once '../../traitement/kpay_fonction.php';
require_once '../../traitement/fonction.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location:payer.php');
    exit();
}

$telephone = '221764019647';
$montant = 100;
$mode_validation = $_POST['mode_validation'] ?? 'OTP';

if (empty($telephone)) {
    die("Numéro de téléphone obligatoire");
}

/* if ($montant < 3000) {
    die("Montant minimum 3000 FCFA");
} */

$num_etu = $_SESSION['num_etu'];

try {

    /*
    |--------------------------------------------------------------------------
    | Authentification
    |--------------------------------------------------------------------------
    */
    $token = getKpayToken();

    /*
    |--------------------------------------------------------------------------
    | Paiement
    |--------------------------------------------------------------------------
    */
    $result = initiatePayment(
        $token,
        $telephone,
        $montant,
        $num_etu
    );

    /*
    |--------------------------------------------------------------------------
    | Vérification réponse
    |--------------------------------------------------------------------------
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
    |--------------------------------------------------------------------------
    | Sauvegarde session
    |--------------------------------------------------------------------------
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
    |--------------------------------------------------------------------------
    | Référence transaction
    |--------------------------------------------------------------------------
    */
    $_SESSION['transactionId'] = $kpayReference;

    /*
    |--------------------------------------------------------------------------
    | Enregistrement base de données
    |--------------------------------------------------------------------------
    */

    
    savePaiementTemp(
        $num_etu,
        $telephone,
        $montant,
        $kpayReference,
        'PENDING'
    );
    

    /* 
    |--------------------------------------------------------------------------
    | Redirections
    |--------------------------------------------------------------------------
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