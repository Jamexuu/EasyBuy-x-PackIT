<?php
include 'classes/Cart.php';
include 'classes/Auth.php';

Auth::start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$user = Auth::getUser();
if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['cart_item_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Cart item ID is required']);
    exit;
}

$cart = new Cart();
$result = $cart->deleteCartItem($data['cart_item_id']);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete item']);
}
