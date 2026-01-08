<?php

require_once 'classes/Product.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $product = new Product();
    $discountedProducts = $product->getDiscountedProducts();
    echo json_encode($discountedProducts);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
}