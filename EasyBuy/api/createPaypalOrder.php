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

$amount = $data['amount'] ?? null;
$description = $data['description'] ?? 'EasyBuy Order';

if (!$amount) {
    http_response_code(400);
    echo json_encode(['error' => 'Amount is required']);
    exit();
}

try {
    $paypalOrder = paypalCreateOrder($amount, $description, 'USD');
    
    if (!$paypalOrder) {
        error_log('PayPal order creation returned null. Amount: ' . $amount . ', Description: ' . $description);
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to create PayPal order. Check server logs for details.']);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'orderId' => $paypalOrder['id']
    ]);
    
} catch (Exception $e) {
    error_log('PayPal order creation error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Internal server error: ' . $e->getMessage()]);
}
