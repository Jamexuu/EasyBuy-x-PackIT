<?php
include 'classes/Product.php';

header('Content-Type: application/json');

$product = new Product();
$keyword = $_GET['keyword'] ?? '';
$products = $product->searchProducts($keyword);
echo json_encode($products);
