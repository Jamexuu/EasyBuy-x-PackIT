<?php
include 'classes/Auth.php';

Auth::start();
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$_SESSION['checkout_items'] = $data['cart_ids'];
echo json_encode(['success' => true]);
