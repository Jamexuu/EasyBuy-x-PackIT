<?php

require_once 'classes/Product.php';
require_once 'classes/Auth.php';

$auth = new Auth();
$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] != 'POST'){
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

$productData = [

    'product_name' => $input['product_name'] ?? null,
    'category' => $input['category'] ?? null,
    'size' => $input['size'] ?? null,
    'price' => $input['price'] ?? null,
    'stocks' => $input['stocks'] ?? null,
    'weight_grams' => $input['weight_grams'] ?? null,
    'is_sale' => $input['is_sale'] ?? 0,
    'sale_percentage' => $input['sale_percentage'] ?? 0,
    'image' => $input['image'] ?? null,
];


if (!$productData['product_name'] || !$productData['category'] || !$productData['price'] || !$productData['image']) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

$product = new Product();
try {
    $product->addProduct($productData);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
}

