<?php

require_once 'classes/Cart.php';
require_once 'classes/Auth.php';

$auth = new Auth();

$auth->requireAuth();

if ($_SERVER['REQUEST_METHOD'] != 'PUT'){
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

$cart = new Cart();
$itemId = $input['itemId'] ?? null;
$quantity = $input['quantity'] ?? null;

if ($quantity < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Quantity must be at least 1']);
    exit();
}

try {
    $cart->updateCartItemQuantity($itemId, $quantity);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
}

