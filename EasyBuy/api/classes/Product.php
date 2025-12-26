<?php

include 'Database.php';

class Product {
    private $db;

    function __construct() {
        $this->db = new Database();
    }

    function getAllProducts() {
        $query = "SELECT * FROM products";

        $stmt = $this->db->executeQuery($query);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        return $data;
    }

    function getProductById($productId) {
        $query = "SELECT * FROM products WHERE id = ?";
        $params = [$productId];
        return $this->db->executeQuery($query, $params);
    }

    function searchProducts($keyword) {
        $query = "SELECT * FROM products WHERE product_name LIKE ? OR category LIKE ?";
        $likeKeyword = '%' . $keyword . '%';
        $params = [$likeKeyword, $likeKeyword];
        
        $stmt = $this->db->executeQuery($query, $params);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        return $data;
    }
}