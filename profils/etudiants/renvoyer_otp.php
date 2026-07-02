<?php

session_start();

require_once('../../traitement/kpay_fonction.php');

if (
    empty($_SESSION['paiement']) ||
    empty($_SESSION['paiement']['correlationReference'])
) {

    $_SESSION['error'] = "Votre session de paiement a expiré.";

    header("Location:payer.php");
    exit();
}

try {

    $token = getKpayToken();

    resendOtp(
        $token,
        $_SESSION['paiement']['correlationReference']
    );

    $_SESSION['success'] =
        "Un nouveau code OTP a été envoyé sur votre téléphone.";

} catch (Exception $e) {

    $message = $e->getMessage();

    $json = json_decode($message, true);

    if (
        json_last_error() === JSON_ERROR_NONE &&
        isset($json['message'])
    ) {

        $_SESSION['error'] = $json['message'];

    } else {

        $_SESSION['error'] = $message;

    }

}

header("Location: otp.php");
exit();