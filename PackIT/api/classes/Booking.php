<?php

include 'Database.php';

class Booking {
    private $db;

    function __construct(){
        $this->db = new Database();
    }

    function createBooking($bookingData){
        $sql = "INSERT INTO bookings (
                    user_id, pickup_house, pickup_barangay, pickup_municipality, pickup_province,
                    pickup_lat, pickup_lng, drop_house, drop_barangay, drop_municipality, drop_province,
                    drop_lat, drop_lng, vehicle_type, distance_km, base_amount, distance_amount,
                    door_to_door_amount, total_amount, payment_method
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $bookingData['userId'],
            $bookingData['pickupHouse'] ?? '',
            $bookingData['pickupBarangay'] ??  '',
            $bookingData['pickupMunicipality'],
            $bookingData['pickupProvince'],
            $bookingData['pickupLat'] ?? null,
            $bookingData['pickupLng'] ?? null,
            $bookingData['dropHouse'] ?? '',
            $bookingData['dropBarangay'] ?? '',
            $bookingData['dropMunicipality'],
            $bookingData['dropProvince'],
            $bookingData['dropLat'] ??  null,
            $bookingData['dropLng'] ?? null,
            $bookingData['vehicleType'],
            $bookingData['distanceKm'] ?? null,
            $bookingData['baseAmount'],
            $bookingData['distanceAmount'],
            $bookingData['doorToDoorAmount'],
            $bookingData['totalAmount'],
            $bookingData['paymentMethod'] ?? 'paypal'
        ];

        $stmt = $this->db->executeQuery($sql, $params);
        mysqli_stmt_close($stmt);

        return $this->db->lastInsertId();
    }

    function getBookingsByUser($userId){
        $sql = "SELECT * FROM bookings WHERE user_id = ?  ORDER BY created_at DESC";
        $stmt = $this->db->executeQuery($sql, [$userId]);
        $result = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }

    function getBookingById($bookingId){
        $sql = "SELECT b.*, 
                       u.first_name, u.last_name, u.email, u.contact_number,
                       d.vehicle_type as driver_vehicle, du.first_name as driver_first_name, du.last_name as driver_last_name
                FROM bookings b
                LEFT JOIN users u ON b.user_id = u.id
                LEFT JOIN drivers d ON b.driver_id = d.id
                LEFT JOIN users du ON d.user_id = du.id
                WHERE b.id = ?";
        
        $stmt = $this->db->executeQuery($sql, [$bookingId]);
        $result = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        
        return $result[0] ?? null;
    }

    function getPendingBookings(){
        $sql = "SELECT b.*, u.first_name, u.last_name, u.contact_number
                FROM bookings b
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.tracking_status = 'pending' AND b.payment_status = 'paid'
                ORDER BY b.created_at ASC";
        
        $stmt = $this->db->executeQuery($sql, []);
        $result = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }

    function acceptBooking($bookingId, $driverId){
        $sql = "UPDATE bookings SET driver_id = ?, tracking_status = 'accepted' WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, [$driverId, $bookingId]);
        mysqli_stmt_close($stmt);
        
        return true;
    }

    function updateTrackingStatus($bookingId, $status){
        $sql = "UPDATE bookings SET tracking_status = ? WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, [$status, $bookingId]);
        mysqli_stmt_close($stmt);
        
        return true;
    }

    function updatePaymentStatus($bookingId, $status){
        $sql = "UPDATE bookings SET payment_status = ?  WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, [$status, $bookingId]);
        mysqli_stmt_close($stmt);
        
        return true;
    }

    function getDriverBookings($driverId){
        $sql = "SELECT b.*, u.first_name, u.last_name, u. contact_number
                FROM bookings b
                LEFT JOIN users u ON b.user_id = u. id
                WHERE b.driver_id = ?
                ORDER BY b.created_at DESC";
        
        $stmt = $this->db->executeQuery($sql, [$driverId]);
        $result = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }
}