<?php

require_once __DIR__ . '/../config.php';

if ($_ENV['PAYPAL_SANDBOX'] === 'true') {
    define('PAYPAL_BASE', 'https://api-m.sandbox.paypal.com');
} else {
    define('PAYPAL_BASE', 'https://api-m.paypal.com');
}

function paypalGetAccessToken() {
    $url = PAYPAL_BASE . '/v1/oauth2/token';

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $_ENV['PAYPAL_CLIENT_ID'] . ':' . $_ENV['PAYPAL_CLIENT_SECRET']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_HTTPHEADER,[
        'Accept: application/json',
        'Accept-Language: en_US',
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        error_log('cURL error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300){
        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }
    error_log('PayPal token request failed. HTTP code: ' . $httpCode . ' Response: ' . $response);
    return null;
}

function paypalCreateOrder($amount, $productName, $currency = 'USD'){
    $token = paypalGetAccessToken();
    if (!$token) {
        error_log('PayPal access token not received');
        return null;
    }

    $url = PAYPAL_BASE . '/v2/checkout/orders';

    $body = [
        'intent' => 'CAPTURE',
        'purchase_units' => [[
            'amount' => [
                'currency_code' => $currency,
                'value' => number_format($amount, 2, '.', ''),
                'breakdown' => [
                    'item_total' => [
                        'currency_code' => $currency,
                        'value' => number_format($amount, 2, '.', '')
                    ]
                ]
            ],
            'items' => [[
                'name' => $productName,
                'unit_amount' => [
                    'currency_code' => $currency,
                    'value' => number_format($amount, 2, '.', '')
                ],
                'quantity' => '1'
            ]]
        ]]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

    $response = curl_exec($ch);
    if ($response === false){
        error_log('cURL error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($httpCode >= 200 && $httpCode < 300){
        return json_decode($response, true);
    }
    error_log('PayPal order creation failed. HTTP code: ' . $httpCode . ' Response: ' . $response);
    return null;
} 

function paypalCaptureOrder($orderId) {
    $token = paypalGetAccessToken();
    if (!$token) return null;

    $url = PAYPAL_BASE . '/v2/checkout/orders/' . urlencode($orderId) . '/capture';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_POST, true);

    $response = curl_exec($ch);
    if ($response === false){
        error_log('cURL error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($httpCode >= 200 && $httpCode < 300){
        return json_decode($response, true);
    }
    error_log('PayPal capture failed. HTTP code: ' . $httpCode . ' Response: ' . $response);
    return null;
}
