<?php
require_once 'classes/Order.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$orderId = $input['orderId'] ?? null;
$driverId = $input['driverId'] ?? null;

if (!$orderId || !$driverId) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID and Driver ID are required']);
    exit();
}

try {
    $order = new Order();
    
    // Update order status to "picked up" when driver accepts
    $success = $order->updateStatus($orderId, 'picked up');
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Order accepted successfully',
            'orderId' => $orderId
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to accept order']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error accepting order: ' . $e->getMessage()]);
}
