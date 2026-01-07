<?php

require_once 'classes/Order.php';
require_once 'classes/Product.php';
require_once 'classes/Auth.php';

$auth = new Auth();

$auth->start();
$auth->requireAdmin();


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $order = new Order();
    $product = new Product();

    $placedOrderCount = $order->getPlacedOrderCount();
    $pickedUpOrderCount = $order->getPickedUpOrderCount();
    $productCount = $product->getProductCount();

    $response = [
        'placedOrderCount' => $placedOrderCount,
        'pickedUpOrderCount' => $pickedUpOrderCount,
        'allProducts' => $productCount
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}