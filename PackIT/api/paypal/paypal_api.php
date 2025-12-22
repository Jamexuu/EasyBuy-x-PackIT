<?php
// paypal_api.php
declare(strict_types=1);

// FIX: Point back to the frontend/booking folder for these files
require_once __DIR__ . "/../../frontend/booking/booking_state.php";
require_once __DIR__ . "/../../frontend/booking/fare_rules.php";

// This file is in the same folder (api/paypal), so this is fine
require_once __DIR__ . "/paypal_config.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

function generateAccessToken(): string {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_BASE_URL . "/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ":" . PAYPAL_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }
    curl_close($ch);

    $data = json_decode($response, true);
    if (!isset($data['access_token'])) {
        throw new Exception("Failed to get Access Token: " . ($data['error_description'] ?? 'Unknown error'));
    }
    return $data['access_token'];
}

function createOrder($amount) {
    $token = generateAccessToken();
    
    $data = [
        "intent" => "CAPTURE",
        "purchase_units" => [[
            "amount" => [
                "currency_code" => "PHP",
                "value" => number_format((float)$amount, 2, '.', '')
            ]
        ]]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_BASE_URL . "/v2/checkout/orders");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $token
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

function captureOrder($orderId) {
    $token = generateAccessToken();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_BASE_URL . "/v2/checkout/orders/$orderId/capture");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $token
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// --- Main Handler Logic ---
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

try {
    if ($action === 'create_order') {
        $state = get_booking_state();
        
        $base = (int)($state["base_amount"] ?? 0);
        
        // Ensure regions are valid strings
        $pRegion = isset($state["pickup_region"]) ? (string)$state["pickup_region"] : null;
        $dRegion = isset($state["drop_region"]) ? (string)$state["drop_region"] : null;
        
        $dist = compute_distance_fare($pRegion, $dRegion);
        $door = get_door_to_door_amount((bool)($state["door_to_door"] ?? true));
        
        if ($dist === null) throw new Exception("Invalid Booking State: Distance fare not found.");
        
        $totalAmount = compute_total_fare($base, $dist, $door);

        $order = createOrder($totalAmount);
        echo json_encode($order);

    } elseif ($action === 'capture_order') {
        $orderId = $input['orderID'] ?? '';
        if (!$orderId) throw new Exception("Missing Order ID");

        $capture = captureOrder($orderId);
        
        if (($capture['status'] ?? '') === 'COMPLETED') {
            unset($_SESSION['booking']); 
        }

        echo json_encode($capture);
    } else {
        throw new Exception("Invalid Action");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>