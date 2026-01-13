<?php

require_once 'classes/Auth.php';
require_once 'classes/Database.php';

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

if (!isset($_SESSION['direct_checkout'])) {
    echo json_encode([]);
    exit();
}

$directCheckout = $_SESSION['direct_checkout'];
$productId = $directCheckout['product_id'];
$quantity = $directCheckout['quantity'];

$db = new Database();

$query = "
    SELECT 
        ? as id,
        products.id as product_id,
        products.product_name,
        products.price,
        products.is_sale,
        products.sale_percentage,
        CASE 
            WHEN products.is_sale = 1 THEN ROUND(products.price * (1 - products.sale_percentage/100), 2)
            ELSE products.price
        END AS final_price,
        products.image,
        products.size,
        products.weight_grams,
        products.category,
        ? as quantity
    FROM products 
    WHERE products.id = ?
";

$result = $db->fetch($db->executeQuery($query, [$productId, $quantity, $productId]));

unset($_SESSION['direct_checkout']);

echo json_encode($result);