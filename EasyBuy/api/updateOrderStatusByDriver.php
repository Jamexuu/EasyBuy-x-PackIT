<?php
require_once 'classes/Order.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$orderId = $input['orderId'] ?? null;
$driverId = $input['driverId'] ?? null;
$newStatus = $input['newStatus'] ?? null;

if (!$orderId || !$driverId || !$newStatus) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID, Driver ID, and New Status are required']);
    exit();
}

// Validate that the status is a driver-allowed status
$allowedStatuses = ['picked up', 'in transit', 'order arrived'];
if (!in_array(strtolower($newStatus), $allowedStatuses)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid status. Drivers can only set: ' . implode(', ', $allowedStatuses)]);
    exit();
}

try {
    $order = new Order();
    
    // Update order status
    $success = $order->updateStatus($orderId, $newStatus);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Order status updated successfully',
            'orderId' => $orderId,
            'newStatus' => $newStatus
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update order status']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error updating order status: ' . $e->getMessage()]);
}
