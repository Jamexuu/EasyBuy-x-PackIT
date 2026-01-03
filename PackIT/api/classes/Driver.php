<?php

include 'Database.php';

class Driver {
    private $db;

    function __construct(){
        $this->db = new Database();
    }

    function registerDriver($userId, $vehicleType, $licensePlate = null){
        $sql = "INSERT INTO drivers (user_id, vehicle_type, license_plate) VALUES (?, ?, ?)";
        
        $params = [$userId, $vehicleType, $licensePlate];

        $stmt = $this->db->executeQuery($sql, $params);
        mysqli_stmt_close($stmt);

        return $this->db->lastInsertId();
    }

    function getDriverByUserId($userId){
        $sql = "SELECT d.*, u.first_name, u.last_name, u. email, u.contact_number
                FROM drivers d
                LEFT JOIN users u ON d.user_id = u.id
                WHERE d. user_id = ?";
        
        $stmt = $this->db->executeQuery($sql, [$userId]);
        $result = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        
        return $result[0] ?? null;
    }

    function updateAvailability($driverId, $isAvailable){
        $sql = "UPDATE drivers SET is_available = ? WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, [$isAvailable, $driverId]);
        mysqli_stmt_close($stmt);
        
        return true;
    }

    function getAvailableDrivers($vehicleType = null){
        if ($vehicleType) {
            $sql = "SELECT d.*, u.first_name, u. last_name, u.contact_number
                    FROM drivers d
                    LEFT JOIN users u ON d.user_id = u. id
                    WHERE d.is_available = 1 AND d.vehicle_type = ? ";
            $stmt = $this->db->executeQuery($sql, [$vehicleType]);
        } else {
            $sql = "SELECT d.*, u.first_name, u.last_name, u.contact_number
                    FROM drivers d
                    LEFT JOIN users u ON d.user_id = u.id
                    WHERE d.is_available = 1";
            $stmt = $this->db->executeQuery($sql, []);
        }
        
        $result = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }
}