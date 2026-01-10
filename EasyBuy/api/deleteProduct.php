<?php

require_once 'classes/Product.php';
require_once 'classes/Auth.php';

$auth = new Auth();
$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] != 'DELETE'){
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$productId = $input['productId'] ?? null;

if (!$productId) {
    http_response_code(400);
    echo json_encode(['error' => 'Product ID is required']);
    exit();
}

$product = new Product();
try {
    $productData = $product->getProductById($productId);
    
    if ($productData && !empty($productData['image'])) {
        $imagePath = '../' . $productData['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    $product->deleteProduct($productId);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
}