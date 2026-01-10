<?php

require_once 'Database.php';

class Product {
    private $db;

    function __construct() {
        $this->db = new Database();
    }

    function getAllProducts() {
        $query = "SELECT * FROM products";

        $stmt = $this->db->executeQuery($query);
        $data = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);

        return $data;
    }

    function getProductById($productId) {
        $query = "SELECT * FROM products WHERE id = ?";
        $params = [$productId];
        
        $stmt = $this->db->executeQuery($query, $params);
        $data = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        
        return $data;
    }

    function searchProducts($keyword) {
        $query = "SELECT * FROM products WHERE product_name LIKE ? OR category LIKE ?";
        $likeKeyword = '%' . $keyword . '%';
        $params = [$likeKeyword, $likeKeyword];
        
        $stmt = $this->db->executeQuery($query, $params);
        $data = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);

        return $data;
    }

    function getProductCount() {
        $query = "SELECT COUNT(*) as count FROM products";

        $stmt = $this->db->executeQuery($query);
        $data = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);

        return $data[0]['count'];
    }

    function getDiscountedProducts(){
        $query = "SELECT *, 
                    ROUND(price * (1 - sale_percentage/100), 2) as sale_price
                  FROM products 
                  WHERE is_sale = 1 
                  ORDER BY category";
        
        $stmt = $this->db->executeQuery($query);
        $data = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        return $data;
    }

    function deleteProduct($productId){
        $query = "DELETE FROM products WHERE id = ?";
        $params = [$productId];

        $stmt = $this->db->executeQuery($query, $params);
        mysqli_stmt_close($stmt);
    }

    function addProduct($productData){
        $query = "INSERT INTO products (product_name, category, price, stocks, is_sale, sale_percentage, weight_grams, size, image)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $productData['product_name'],
            $productData['category'],
            $productData['price'],
            $productData['stocks'],
            $productData['is_sale'],
            $productData['sale_percentage'],
            $productData['weight_grams'],
            $productData['size'],
            $productData['image']
        ];

        $stmt = $this->db->executeQuery($query, $params);
        mysqli_stmt_close($stmt);
    }

    function updateProduct($productId, $productData){
        $query = "UPDATE products 
                  SET product_name = ?, category = ?, price = ?, stocks = ?, is_sale = ?, sale_percentage = ?, weight_grams = ?, size = ?, image = ?
                  WHERE id = ?";
        
        $params = [
            $productData['product_name'],
            $productData['category'],
            $productData['price'],
            $productData['stocks'],
            $productData['is_sale'],
            $productData['sale_percentage'],
            $productData['weight_grams'],
            $productData['size'],
            $productData['image'],
            $productId
        ];

        $stmt = $this->db->executeQuery($query, $params);
        mysqli_stmt_close($stmt);
    }
}