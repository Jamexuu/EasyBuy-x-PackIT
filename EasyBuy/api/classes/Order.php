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

    function addOrder($userId, $totalAmount, $totalWeight, $paymentMethod, $shippingFee, $cartItems){
        $query = "INSERT INTO orders (user_id, total_amount, total_weight_grams, payment_method, shipping_fee) 
                VALUES (?, ?, ?, ?, ?)";
        
        $params = [$userId, $totalAmount, $totalWeight, $paymentMethod, $shippingFee];
        $this->db->executeQuery($query, $params);
        
        $orderId = $this->db->lastInsertId();
        
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

    function getAllOrdersWithItems(){
        $ordersQuery = "SELECT o.id, o.user_id, o.total_amount, o.order_date, o.status, u.email
                        FROM orders o
                        INNER JOIN users u ON o.user_id = u.id
                        ORDER BY o.order_date DESC";
        $ordersResult = $this->db->executeQuery($ordersQuery);
        $orders = $this->db->fetch($ordersResult);
        
        $result = [];
        foreach ($orders as $order) {
            $itemsQuery = "SELECT product_name, quantity, product_price
                          FROM order_items
                          WHERE order_id = ?";
            $itemsResult = $this->db->executeQuery($itemsQuery, [$order['id']]);
            $items = $this->db->fetch($itemsResult);
            
            $result[] = [
                'orderID' => $order['id'],
                'userID' => $order['user_id'],
                'userEmail' => $order['email'],
                'totalAmount' => $order['total_amount'],
                'orderDate' => $order['order_date'],
                'status' => $order['status'],
                'itemCount' => count($items),
                'items' => $items
            ];
        }
        
        return $result;
    }
}