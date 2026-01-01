<?php
include 'classes/Auth.php';

Auth::start();
header('Content-Type: application/json');
Auth::requireAuth();


$data = file_get_contents('php://input');

$_SESSION['checkout_items'] = json_decode($data, true);
echo json_encode(['success' => true]);