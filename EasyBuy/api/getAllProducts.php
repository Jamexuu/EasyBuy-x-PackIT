<?php
include 'classes/Product.php';
header('Content-Type: application/json');

$product = new Product();
$products = $product->getAllProducts();
echo json_encode($products);