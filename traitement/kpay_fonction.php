<?php

function getKpayToken()
{
    $clientId = '9178facc-F9a0-C44c-120d569a';
    $clientSecret = 'C2b7668b-Fec8-dC37-b6e60ed8';

    $url = 'https://encaissements-test.kpay-api.com/v1/auth/token'
        . '?clientId=' . urlencode($clientId)
        . '&clientSecret=' . urlencode($clientSecret);

    $ch = curl_init($url);

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if (!isset($data['access_token'])) {
        throw new Exception('Token introuvable');
    }

    return $data['access_token'];
}

function initiatePayment(
    $token,
    $telephone,
    $montant,
    $numEtu
) {
    $url = 'https://encaissements-test.kpay-api.com/v1/payment/initiate_payment';
    $correlationReference = 'LOG_' . date('YmdHis') . '_' . $numEtu;

    $payload = [
        'merchant' => [
            'fullName' => 'madiop',
            'phoneNumber' => '221784413400'
        ],
        'customerPhoneNumber' => '221764019647',
        'amount' => 100,
        'description' => 'Paiement logement étudiant',
        'callbackUrl' => 'https://campuscoud.com/',
        'useUniqueWallet' => false,
        'inAppValidation' => false,
        'qrValidation' => false,
        'correlationReference' => $correlationReference
    ];

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Bearer {$token}"
        ],
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    curl_close($ch);
    return json_decode($response, true);
}

function confirmPayment(
    $token,
    $otp,
    $kpayReference,
    $telephone
) {
    $url = 'https://encaissements-test.kpay-api.com/v1/payment/confirm_payment';

    $payload = [
        'otp' => $otp,
        'kpayReference' => $kpayReference,
        'customerPhoneNumber' => $telephone
    ];

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ],
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    /* if ($httpCode != 200) {
        throw new Exception(
            'OTP invalide ou paiement refusé.'
        );
    } */

    return json_decode($response, true);
}

function searchPaymentByCorrelationReference(
    $token,
    $correlationReference
) {
    $url =
        'https://encaissements-test.kpay-api.com/v1/payment/search_payment_by_correlation_reference'
        . '?correlation_reference='
        . urlencode($correlationReference);

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token
        ]
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    curl_close($ch);

    return json_decode($response, true);
}

function savePaiementTemp(
    $num_etu,
    $telephone,
    $montant,
    $kpayReference,
    $statut = 'PENDING'
) {
    global $connexion;

    $sql = '
        INSERT INTO codif_paiement_kpay(
            num_etu,
            telephone,
            montant,
            kpay_reference,
            statut,
            date_creation
        )
        VALUES(
            ?, ?, ?, ?, ?, NOW()
        )
    ';

    $stmt = mysqli_prepare($connexion, $sql);

    if (!$stmt) {
        throw new Exception(
            'Erreur préparation requête : '
            . mysqli_error($connexion)
        );
    }

    mysqli_stmt_bind_param(
        $stmt,
        'ssdss',
        $num_etu,
        $telephone,
        $montant,
        $kpayReference,
        $statut
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception(
            'Erreur insertion : '
            . mysqli_stmt_error($stmt)
        );
    }

    $id = mysqli_insert_id($connexion);

    mysqli_stmt_close($stmt);

    return $id;
}

function updatePaiementKpay(
    $kpayReference,
    $statut
) {
    global $connexion;

    $sql = '
        UPDATE codif_paiement_kpay
        SET
            statut = ?,
            date_creation = NOW()
        WHERE kpay_reference = ?
    ';

    $stmt = mysqli_prepare($connexion, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        'ss',
        $statut,
        $kpayReference
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
}
?>