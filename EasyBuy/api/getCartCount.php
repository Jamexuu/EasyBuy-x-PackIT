<?php
include 'classes/Cart.php';
include 'classes/Auth.php';

Auth::start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

$user = Auth::getUser();

if (!$user) {
    echo json_encode(['count' => 0]);
    exit();
}

$cart = new Cart();
$items = $cart->getCartItems($user['id']);

$totalQuantity = 0;
foreach ($items as $item) {
    $totalQuantity += $item['quantity'];
}

echo json_encode(['count' => $totalQuantity]);
