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
        $result = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        
        $data = $result[0] ?? null;

        if ($requiredRole !== null && $data) {
            if (!isset($data['role']) || $data['role'] !== $requiredRole) {
                return false;
            }
        }

        return $data;
    }

    function getUserDetails($userId){
        $sql = "SELECT u.id, u.first_name, u.last_name, u.email, u.contact_number, u.role,
                       a.house_number, a.street, a.lot, a.block, a.barangay, 
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
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
        $stmt = $this->db->executeQuery($sql, [$email]);
        $result = $this->db->fetch($stmt);
        
        return $result[0]['count'] > 0;
    }

    function findByEmail($email){
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->db->executeQuery($sql, [$email]);
        $result = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);

        return $result[0] ?? false;
    }

    function getPhoneNumber($email){
        $sql = "SELECT contact_number FROM users WHERE email = ?";
        $stmt = $this->db->executeQuery($sql, [$email]);
        $result = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);

        return $result[0]['contact_number'] ?? null;
    }

    function updatePassword($email, $newPassword){
        $sql = "UPDATE users SET password = ? WHERE email = ?";
        $hashPassword = md5($newPassword);

        $params = [
            $hashPassword,
            $email
        ];

        $stmt = $this->db->executeQuery($sql, $params);
        $affectedRows = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);

        return $affectedRows > 0;
    }
}