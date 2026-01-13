<?php

include 'classes/Auth.php';

Auth::start();
header('Content-Type: application/json');

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
$productId = $data['productId'] ?? null;
$quantity = $data['quantity'] ?? 1;

if (!$productId || $quantity <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

$_SESSION['direct_checkout'] = [
    'product_id' => $productId,
    'quantity' => $quantity,
    'timestamp' => time()
];

echo json_encode([
    'success' => true,
    'message' => 'Direct checkout session created'
]);