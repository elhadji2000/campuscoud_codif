<?php

session_start();
require_once '../../traitement/fonction.php';
require_once '../../traitement/kpay_fonction.php';

if (
    empty($_GET['action']) ||
    empty($_GET['reference'])
) {
    header("Location: search.php");
    exit();
}

$action = $_GET['action'];
$reference = $_GET['reference'];
$kpay_reference = $_GET['kpay_reference'];

try {

    $token = getKpayToken();

    if ($action == "cancel") {

        cancelPayment($token, $reference);
        updatePaiementKpay($kpay_reference,'CANCELED');
        //supprimerPaiement($connexion, $id_paie, $deleted_by)

        $_SESSION['success'] =
            "Le paiement a été annulé avec succès.";

    } elseif ($action == "refund") {

        refundPayment($token, $reference);
        updatePaiementKpay($kpay_reference,'REFUNDED');
        $deleted_by = $_SESSION['username'];
        $id_paie = getIdPayByReference($kpay_reference);
        supprimerPaiement($connexion, $id_paie, $deleted_by);

        $_SESSION['success'] =
            "Le remboursement a été effectué avec succès.";

    } else {

        $_SESSION['error'] =
            "Action inconnue.";

    }

} catch (Exception $e) {

    $message = $e->getMessage();

    /*
    |--------------------------------------------------------------------------
    | Si KPay renvoie du JSON
    |--------------------------------------------------------------------------
    */

    $json = json_decode($message, true);

    if (
        json_last_error() === JSON_ERROR_NONE &&
        isset($json['message'])
    ) {

        $message = $json['message'];
    }

    /*
    |--------------------------------------------------------------------------
    | Nettoyage de l'encodage
    |--------------------------------------------------------------------------
    */

    $message = str_replace("expirÃ©", "expiré", $message);

    /*
    |--------------------------------------------------------------------------
    | Messages personnalisés
    |--------------------------------------------------------------------------
    */

    if (
        stripos($message, "session a expir") !== false
    ) {

        $_SESSION['error'] =
            "La session KPay a expiré. Veuillez recommencer l'opération.";

    } elseif (
        stripos($message, "identifiant") !== false
    ) {

        $_SESSION['error'] =
            "Impossible de se connecter au serveur KPay.";

    } elseif (
        stripos($message, "réessayer") !== false ||
        stripos($message, "reessayer") !== false
    ) {

        $_SESSION['error'] =
            "Le serveur KPay est momentanément indisponible. Veuillez réessayer plus tard.";

    } elseif (
        stripos($message, "not found") !== false
    ) {

        $_SESSION['error'] =
            "Le service demandé est introuvable.";

    } elseif (
        stripos($message, "timeout") !== false
    ) {

        $_SESSION['error'] =
            "Le serveur KPay ne répond pas actuellement.";

    } elseif (
        stripos($message, "failed to connect") !== false
    ) {

        $_SESSION['error'] =
            "Impossible de joindre le serveur KPay.";

    } else {

        $_SESSION['error'] = $message;

    }

}

header("Location: search.php");
exit();