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

        return $userId;
    }

    function insertUser($userData, $role = 'user'){
        $sql = "INSERT INTO users (first_name, last_name, email, password, contact_number, role) 
            VALUES (?, ?, ?, ?, ?, ?)";

        $hashPassword = password_hash($userData['password'], PASSWORD_DEFAULT);

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
        $sql = "INSERT INTO addresses (user_id, house_number, street, subdivision, landmark, barangay, city, province, postal_code) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $addressData['userId'],
            $addressData['houseNumber'] ?? '',
            $addressData['street'] ?? '',
            $addressData['subdivision'] ?? '',
            $addressData['landmark'] ?? '',
            $addressData['barangay'] ?? '',
            $addressData['city'],
            $addressData['province'],
            $addressData['postalCode']
        ];

        $stmt = $this->db->executeQuery($sql, $params);
        mysqli_stmt_close($stmt);
    }

    function login($email, $password, $requiredRole = null){
        $sql = "SELECT * FROM users WHERE email = ?";
        $params = [$email];

        $stmt = $this->db->executeQuery($sql, $params);
        $result = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        
        $data = $result[0] ?? null;

        if (!$data || !password_verify($password, $data['password'])) {
            return false;
        }

        if ($requiredRole !== null && $data) {
            if (! isset($data['role']) || $data['role'] !== $requiredRole) {
                return false;
            }
        }

        return $data;
    }

    function getUserDetails($userId){
        $sql = "SELECT u.id, u.first_name, u.last_name, u.email, u.contact_number, u.role, u.created_at,
                       a.house_number, a. street, a.subdivision, a. landmark, a.barangay, 
                       a.city, a.province, a.postal_code
                FROM users u
                LEFT JOIN addresses a ON u.id = a.user_id
                WHERE u.id = ?";
        
        $params = [$userId];
        
        $stmt = $this->db->executeQuery($sql, $params);
        $result = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        
        return $result[0] ?? null;
    }

    function emailExists($email){
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->db->executeQuery($sql, [$email]);
        $result = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        
        return ! empty($result);
    }
}