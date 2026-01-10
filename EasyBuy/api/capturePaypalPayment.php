<?php

require_once 'classes/Auth.php';
require_once 'paypal/paypalFunctions.php';

Auth::start();
Auth::requireAuth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$paypalOrderId = $data['paypalOrderId'] ?? null;

if (!$paypalOrderId) {
    http_response_code(400);
    echo json_encode(['error' => 'PayPal Order ID is required']);
    exit();
}

try {
    $captureResult = paypalCaptureOrder($paypalOrderId);
    
    if (!$captureResult) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to capture PayPal payment']);
        exit();
    }
    
    // Extract transaction ID from capture result
    $transactionId = $captureResult['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
    $status = $captureResult['status'] ?? 'UNKNOWN';
    
    echo json_encode([
        'success' => true,
        'transactionId' => $transactionId,
        'status' => $status,
        'captureResult' => $captureResult
    ]);
    
} catch (Exception $e) {
    error_log('PayPal capture error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
