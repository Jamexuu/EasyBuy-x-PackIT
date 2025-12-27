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
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$cart = new Cart();
$data = $cart->getCartItems($user['id']);
echo json_encode($data);