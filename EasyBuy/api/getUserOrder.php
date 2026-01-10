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

$userId = $user['id'];

$order = new Order();
$orders = $order->getUserOrders($userId);

echo json_encode(['success' => true, 'orders' => $orders]);