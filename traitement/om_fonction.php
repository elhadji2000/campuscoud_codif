<?php
define('ORANGE_API_KEY', 'X-Api-Key');
define('ORANGE_MERCHANT_CODE', '123456');
define('ORANGE_MERCHANT_NAME', 'Campus COUD');
define('ORANGE_CALLBACK_URL', 'https://campuscoud.com/orange/callback.php');
define('ORANGE_SUCCESS_URL', 'https://campuscoud.com/orange/success.php');
define('ORANGE_CANCEL_URL', 'https://campuscoud.com/orange/cancel.php');

function getOrangeToken()
{
    $clientId = '57071b0b-2e96-42b6-8d1c-9a8b61a5b48c';
    $clientSecret = 'd6817ec4-c8b3-45d7-9740-ecbfd1d289dd';

    $url = 'https://api.sandbox.orange-sonatel.com/oauth/v1/token';

    $postFields = http_build_query([
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'grant_type' => 'client_credentials'
    ]);

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json'
        ],
        // Pendant les tests uniquement
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT => 60
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpCode != 200) {
        throw new Exception($response);
    }

    $data = json_decode($response, true);

    if (empty($data['access_token'])) {
        throw new Exception('Token Orange Money introuvable.');
    }

    return $data['access_token'];
}

function generateOrangeQrCode(
    $token,
    $montant,
    $reference
) {
    // Paramètres Orange
    $apiKey = ORANGE_API_KEY;  // votre X-Api-Key
    $merchantCode = ORANGE_MERCHANT_CODE;  // code marchand à 6 chiffres
    $merchantName = 'Campus COUD';

    $url = 'https://api.sandbox.orange-sonatel.com/api/eWallet/v4/qrcode';

    $payload = [
        'amount' => [
            'unit' => 'XOF',
            'value' => (int) $montant
        ],
        'callbackCancelUrl' =>
            'https://campuscoud.com/orange/cancel.php',
        'callbackSuccessUrl' =>
            'https://campuscoud.com/orange/success.php',
        'code' => $merchantCode,
        'metadata' => [
            'reference' => $reference,
            'service' => 'LOGEMENT'
        ],
        'name' => $merchantName,
        'validity' => 900  // 15 minutes
    ];

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer {$token}",
            "X-Api-Key: {$apiKey}",
            'X-Callback-Url: https://campuscoud.com/orange/callback.php',
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 60
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    $data = json_decode($response, true);

    if ($httpCode != 200 && $httpCode != 201) {
        throw new Exception(
            $data['message']
                ?? $data['error']
                ?? 'Erreur lors de la génération du QR Code Orange Money.'
        );
    }

    return $data;
}

function searchOrangeTransactions($token, $filters = [])
{

    $url = "https://api.sandbox.orange-sonatel.com/api/eWallet/v1/transactions";

    $params = [];

    if (!empty($filters['fromDateTime'])) {
        $params['fromDateTime'] = $filters['fromDateTime'];
    }

    if (!empty($filters['toDateTime'])) {
        $params['toDateTime'] = $filters['toDateTime'];
    }

    if (!empty($filters['status'])) {
        $params['status'] = $filters['status'];
    }

    if (!empty($filters['reference'])) {
        $params['reference'] = $filters['reference'];
    }

    if (!empty($filters['transactionId'])) {
        $params['transactionId'] = $filters['transactionId'];
    }

    if (!empty($filters['type'])) {
        $params['type'] = $filters['type'];
    }

    if (!empty($filters['bulkId'])) {
        $params['bulkId'] = $filters['bulkId'];
    }

    $params['page'] = $filters['page'] ?? 0;
    $params['size'] = $filters['size'] ?? 20;

    $url .= '?' . http_build_query($params);

    $ch = curl_init($url);

    curl_setopt_array($ch, [

        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,

        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer ".$token,
            "Accept: application/json"
        ]

    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    $data = json_decode($response, true);

    if ($httpCode != 200) {

        throw new Exception(
            $data['message'] ??
            "Erreur Orange Money."
        );

    }

    return $data;

}
