<?php
include 'Database.php';

class Cart {
    private $db;

    function __construct() {
        $this->db = new Database();
    }

    function addToCart($userId, $productId, $quantity) {
        
        $cartQuery = "SELECT id FROM cart WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
        $cart = $this->db->fetch($this->db->executeQuery($cartQuery, [$userId]));
        
        // check if cart exists, if not create one
        if (empty($cart)) {
            $createCart = "INSERT INTO cart (user_id) VALUES (?)";
            $this->db->executeQuery($createCart, [$userId]);
            $cartId = $this->db->lastInsertId();
        } else {
            $cartId = $cart[0]['id'];
        }
    
        $itemQuery = "SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?";
        $item = $this->db->fetch($this->db->executeQuery($itemQuery, [$cartId, $productId]));
        
        // if item exists, update quantity, else insert new item
        if (!empty($item)) {
            $updateQuery = "UPDATE cart_items SET quantity = quantity + ? WHERE id = ?";
            $this->db->executeQuery($updateQuery, [$quantity, $item[0]['id']]);
        } else {
            $insertQuery = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)";
            $this->db->executeQuery($insertQuery, [$cartId, $productId, $quantity]);
        }
        
        return true;
    }

    function getCartItems($userId) {
        $cartQuery = "SELECT id FROM cart WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
        $cart = $this->db->fetch($this->db->executeQuery($cartQuery, [$userId]));
        
        if (empty($cart)) {
            return [];
        }
        
        $cartId = $cart[0]['id'];
        $itemsQuery = "
            SELECT 
                cart_items.*,
                products.product_name,
                products.price,
                products.image,
                products.size,
                products.category
            FROM cart_items 
            INNER JOIN products ON cart_items.product_id = products.id 
            WHERE cart_items.cart_id = ?
        ";
        return $this->db->fetch($this->db->executeQuery($itemsQuery, [$cartId]));
    }

    function deleteCartItem($itemId) {
        $deleteQuery = "DELETE FROM cart_items WHERE id = ?";
        $this->db->executeQuery($deleteQuery, [$itemId]);
        return true;
    }

    function getCartSummary($userId) {
        $cartQuery = "SELECT id FROM cart WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
        $cart = $this->db->fetch($this->db->executeQuery($cartQuery, [$userId]));
        
        if (empty($cart)) {
            return ['subtotal' => 0, 'shipping' => 0, 'total' => 0];
        }
        
        $cartId = $cart[0]['id'];
        $summaryQuery = "
            SELECT 
                SUM(products.price * cart_items.quantity) AS subtotal
            FROM cart_items 
            INNER JOIN products ON cart_items.product_id = products.id 
            WHERE cart_items.cart_id = ?
        ";
        $result = $this->db->fetch($this->db->executeQuery($summaryQuery, [$cartId]));
        $subtotal = $result[0]['subtotal'] ?? 0;
        $shipping = ($subtotal > 0) ? 50 : 0; // based ito sa shipping price nila cj
        $total = $subtotal + $shipping;
        
        return ['subtotal' => $subtotal, 'shipping' => $shipping, 'total' => $total];
    }

}