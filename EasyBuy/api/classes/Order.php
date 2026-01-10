<?php

require_once 'Database.php';

class Order{

    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    function readOrders(){
        $query = "SELECT * FROM orders";
        $result = $this->db->executeQuery($query);
        return $this->db->fetch($result);
    }

    function addOrder($userId, $totalAmount, $totalWeight, $paymentMethod, $shippingFee, $cartItems, $paymentStatus = 'pending', $transactionId = null){
        // insert into orders table
        $query = "INSERT INTO orders (user_id, total_amount, total_weight_grams, payment_method, shipping_fee, payment_status, transaction_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [$userId, $totalAmount, $totalWeight, $paymentMethod, $shippingFee, $paymentStatus, $transactionId];
        $this->db->executeQuery($query, $params);
        
        // get the order ID
        $orderId = $this->db->lastInsertId();
        
        // insert each item into order_items table
        $query = "INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity) 
                VALUES (?, ?, ?, ?, ?)";
        
        foreach ($cartItems as $item) {
            $params = [$orderId, $item['product_id'], $item['product_name'], $item['price'], $item['quantity']];
            $this->db->executeQuery($query, $params);
        }
    
        return $orderId;
    }

    function readOrder($orderId){
        $query = "SELECT * FROM orders WHERE id = ?";
        $result = $this->db->executeQuery($query, [$orderId]);
        return $this->db->fetch($result);
    }

    function getPlacedOrderCount(){
        $query = "SELECT COUNT(*) as count FROM orders WHERE status = 'order placed'";
        $result = $this->db->executeQuery($query);
        $data = $this->db->fetch($result);
        return $data[0]['count'];
    }

    function getPickedUpOrderCount(){
        $query = "SELECT COUNT(*) as count FROM orders WHERE status = 'in transit'";
        $result = $this->db->executeQuery($query);
        $data = $this->db->fetch($result);
        return $data[0]['count'];
    }
}