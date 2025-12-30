<?php
include 'classes/Cart.php';
include 'classes/Auth.php';

Auth::start();
header('Content-Type: application/json');

$user = Auth::getUser();
if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// get saved checkout items from session
$cartIds = $_SESSION['checkout_items'] ?? [];

if (empty($cartIds)) {
    echo json_encode([]);
    exit;
}

$cart = new Cart();
$allItems = $cart->getCartItems($user['id']);

// filter only the selected items
$items = array_filter($allItems, function($item) use ($cartIds) {
    return in_array($item['cart_id'], $cartIds);
});


$items = array_values($items);

echo json_encode($items);