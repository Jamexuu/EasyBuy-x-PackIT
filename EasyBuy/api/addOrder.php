<?php

require_once 'classes/Auth.php';
require_once 'classes/Cart.php';
require_once 'classes/Order.php';

Auth::start();

header('Content-Type: application/json');

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['checkout_items'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request data']);
    exit();
}

$order = new Order();

$userId = $user['id'];
$orderItems = $data['checkout_items'];
$paymentMethod = $data['payment_method'] ?? 'cod';
$shippingFee = $data['shipping_fee'] ?? 0;
$subtotal = $data['subtotal'] ?? 0;
$totalWeight = $data['total_weight'] ?? 0;
$totalAmount = $data['total_amount'] ?? 0;
$paymentStatus = $data['payment_status'] ?? 'pending';
$transactionId = $data['transaction_id'] ?? null;

$orderId = $order->addOrder(
    $userId, 
    $totalAmount, 
    $totalWeight, 
    $paymentMethod, 
    $shippingFee, 
    $orderItems,
    $paymentStatus,
    $transactionId
);

if ($orderId) {
    $cart = new Cart();
    
    if (isset($_SESSION['checkout_items']) && !empty($_SESSION['checkout_items'])) {
        $cartItemIds = $_SESSION['checkout_items'];
        $cart->deleteCartItems($cartItemIds);
    }
    
    unset($_SESSION['checkout_items']);
    unset($_SESSION['cart_items']);
    unset($_SESSION['direct_checkout']);
    
    echo json_encode(['success' => true, 'message' => 'Order added successfully.', 'order_id' => $orderId]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to add order.']);
}