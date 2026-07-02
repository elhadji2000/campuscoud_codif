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

        updatePaiementKpay($kpayReference,'SUCCEEDED' );

        $_SESSION['success'] = 'Paiement effectué avec succès.';
        $data = isValider($num_etu);
        $id_val = $data["id_val"];
        $datesys0 = date('Y-m-d');
        $datesys = strtotime($datesys0);
        $user = $_SESSION['username'];
        $an0 = date('Y', $datesys); 
        $an = substr($an0, 2, 2);
        $accronyme = accronyme($user);
        $link = connexionBD();

        $ins00 = "select max(num_ordre_user) as numauto from codif_paiement where an='$an0' and username_user='$user'";  // echo $ins00;
        $exx00 = mysqli_query($link, $ins00);
        $n_rows0 = mysqli_fetch_assoc($exx00);
        $ordre = $n_rows0['numauto'] + 1;
        $quittance = $an . '-' . $accronyme . '-' . $ordre;
        $chaine_libelle = "Mobile_paye";
        $requete = setPaiement_money($id_val, $user, $montant, $chaine_libelle, $quittance, $an0, $ordre, $correlationReference);

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
