<?php

require_once 'classes/Auth.php';
require_once 'classes/Order.php';

Auth::start();

header('Content-Type: application/json');

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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

if (!isset($_GET['order_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Order ID is required']);
    exit();
}

$orderId = $_GET['order_id'];
$userId = $user['id'];

$order = new Order();
$orderData = $order->readOrder($orderId);

if (empty($orderData)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Order not found']);
    exit();
}

if ($orderData[0]['user_id'] != $userId) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit();
}

echo json_encode(['success' => true, 'order' => $orderData[0]]);