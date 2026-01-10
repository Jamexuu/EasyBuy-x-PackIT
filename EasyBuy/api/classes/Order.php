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
            
            // bawas stock for each product
            $updateStockQuery = "UPDATE products SET stocks = stocks - ? WHERE id = ?";
            $stockParams = [$item['quantity'], $item['product_id']];
            $this->db->executeQuery($updateStockQuery, $stockParams);
        }
    
        return $orderId;
    }

    function readOrder($orderId){
        $query = "SELECT * FROM orders WHERE id = ?";
        $result = $this->db->executeQuery($query, [$orderId]);
        return $this->db->fetch($result);
    }
    function getUserOrders($userId){
        $query = "SELECT o.id, o.status, o.total_amount, o.payment_method, o.shipping_fee, o.order_date,
                  oi.id as item_id, oi.product_id, oi.product_name, oi.product_price, oi.quantity,
                  p.image as image_url
                  FROM orders o
                  LEFT JOIN order_items oi ON o.id = oi.order_id
                  LEFT JOIN products p ON oi.product_id = p.id
                  WHERE o.user_id = ?
                  ORDER BY o.order_date DESC";
        $result = $this->db->executeQuery($query, [$userId]);
        $data = $this->db->fetch($result);
        
        $orders = [];
        foreach ($data as $row) {
            $orderId = $row['id'];
            if (!isset($orders[$orderId])) {
                $orders[$orderId] = [
                    'id' => $row['id'],
                    'status' => $row['status'],
                    'total_amount' => $row['total_amount'],
                    'payment_method' => $row['payment_method'],
                    'shipping_fee' => $row['shipping_fee'],
                    'order_date' => $row['order_date'],
                    'items' => []
                ];
            }
            
            if ($row['item_id']) {
                $orders[$orderId]['items'][] = [
                    'product_id' => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'product_price' => $row['product_price'],
                    'quantity' => $row['quantity'],
                    'image_url' => $row['image_url']
                ];
            }
        }
        
        return array_values($orders);
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
    
    function cancelOrder($orderId, $userId){
        $query = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
        $result = $this->db->executeQuery($query, [$orderId, $userId]);
        $order = $this->db->fetch($result);
        
        if (empty($order)) {
            return false;
        }
        
        if ($order[0]['status'] !== 'order placed') {
            return false;
        }
        
        // babalik stock pag cancel
        $getItemsQuery = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
        $itemsResult = $this->db->executeQuery($getItemsQuery, [$orderId]);
        $orderItems = $this->db->fetch($itemsResult);

        foreach ($orderItems as $item) {
            $restoreStockQuery = "UPDATE products SET stocks = stocks + ? WHERE id = ?";
            $this->db->executeQuery($restoreStockQuery, [$item['quantity'], $item['product_id']]);
        }
        
        $query = "DELETE FROM order_items WHERE order_id = ?";
        $this->db->executeQuery($query, [$orderId]);
        
        $query = "DELETE FROM orders WHERE id = ? AND user_id = ?";
        $this->db->executeQuery($query, [$orderId, $userId]);
        
        return true;

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