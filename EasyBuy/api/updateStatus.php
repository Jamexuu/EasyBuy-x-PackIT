<?php

require_once 'classes/Order.php';
require_once 'classes/Auth.php';

$auth = new Auth();
$auth->start();
$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !=  'PUT'){
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$orderId = $input['orderId'] ?? null;
$newStatus = $input['newStatus'] ?? null;

if (!$orderId || !$newStatus){
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$order = new Order();
$success = $order->updateStatus($orderId, $newStatus);
if ($success){
    echo json_encode(['message' => 'Order status updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update order status']);
}