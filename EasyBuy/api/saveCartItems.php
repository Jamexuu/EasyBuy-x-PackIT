<?php
include 'classes/Auth.php';

Auth::start();
header('Content-Type: application/json');
Auth::requireAuth();

$data = json_decode(file_get_contents('php://input'), true);

$_SESSION['checkout_items'] = $data['cart_item_ids'];
echo json_encode(['success' => true]);
