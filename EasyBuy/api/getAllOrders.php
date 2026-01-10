<?php

include 'classes/Order.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

try {
    $order = new Order();
    $orders = $order->getAllOrdersWithItems();
    echo json_encode($orders);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error fetching orders: ' . $e->getMessage()]);
}


