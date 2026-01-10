<?php

require_once 'classes/Auth.php';
require_once 'classes/Order.php';

Auth::start();

header('Content-Type: application/json');

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

$user = Auth::getUser();

if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['order_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Order ID is required']);
    exit();
}

$orderId = $data['order_id'];
$userId = $user['id'];

$order = new Order();
$success = $order->cancelOrder($orderId, $userId);

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Unable to cancel order. Order may not exist or cannot be cancelled at this stage.']);
}