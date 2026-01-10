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

if (!isset($_SESSION['checkout_items']) || empty($_SESSION['checkout_items'])) {
    echo json_encode(['success' => true, 'message' => 'No items to add order.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$order = new Order();

$userId = $user['id'];
$orderItems = $data['checkout_items'];
$paymentMethod = $data['payment_method'];
$shippingFee = $data['shipping_fee'];
$subtotal = $data['subtotal'];
$totalWeight = $data['total_weight'];
$totalAmount = $data['total_amount'];
$paymentStatus = $data['payment_status'] ?? 'pending';
$transactionId = $data['transaction_id'] ?? null;

$success = $order->addOrder(
    $userId, 
    $totalAmount, 
    $totalWeight, 
    $paymentMethod, 
    $shippingFee, 
    $orderItems,
    $paymentStatus,
    $transactionId
);

$cart = new Cart();

if ($success) {
    // Delete only the checked out cart items
    $cartItemIds = $_SESSION['checkout_items'];
    $cart->deleteCartItems($cartItemIds);
    
    unset($_SESSION['checkout_items']);
    unset($_SESSION['cart_items']);
    echo json_encode(['success' => true, 'message' => 'Order added successfully.', 'order_id' => $success]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to add order.']);
}






