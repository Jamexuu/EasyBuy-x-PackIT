<?php

include 'Database.php';

class User {
    private $db;

    function __construct(){
        $this->db = new Database();
    }

    function register($userData, $addressData, $role = 'user'){
        $userId = $this->insertUser($userData, $role);

        $addressData['userId'] = $userId;
        $this->insertAddress($addressData);
    }

    function insertUser($userData, $role = 'user'){
        $sql = "INSERT INTO users (first_name, last_name, email, password, contact_number, role) 
            VALUES (?, ?, ?, ?, ?, ?)";

        $hashPassword = md5($userData['password']);

        $params = [
            $userData['firstName'],
            $userData['lastName'],
            $userData['email'],
            $hashPassword,
            $userData['contactNumber'],
            $role
        ];

        $stmt = $this->db->executeQuery($sql, $params);
        mysqli_stmt_close($stmt);

        return $this->db->lastInsertId();

    }
    function insertAddress($addressData){
        $sql = "INSERT INTO addresses (user_id, house_number, street, lot, block, barangay, city, province, postal_code) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $addressData['userId'],
            $addressData['houseNumber'],
            $addressData['street'],
            $addressData['lot'],
            $addressData['block'],
            $addressData['barangay'],
            $addressData['city'],
            $addressData['province'],
            $addressData['postalCode']
        ];

        $stmt = $this->db->executeQuery($sql, $params);
        mysqli_stmt_close($stmt);
    }

    function login($email, $password, $requiredRole = null){
        $sql = "SELECT * FROM users WHERE email = ? AND password = ?";

        $hashPassword = md5($password);

        $params = [
            $email,
            $hashPassword
        ];

        $stmt = $this->db->executeQuery($sql, $params);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($requiredRole !== null && $data) {
            if (!isset($data['role']) || $data['role'] !== $requiredRole) {
                return false;
            }
        }

        return $data;
    }
}