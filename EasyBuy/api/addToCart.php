<?php

include 'classes/Cart.php';
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

$cart = new Cart();
$success = $cart->addToCart($user['id'], $productId, $quantity);
if ($success) {
    echo json_encode(['message' => 'Product added to cart successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to add product to cart']);
}

