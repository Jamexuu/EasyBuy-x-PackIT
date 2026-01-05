<?php

include 'Database.php';

class Booking {
    private $db;

    function __construct(){
        $this->db = new Database();
    }

    /**
     * Create a booking record including package description/details.
     * Expects keys:
     * - userId
     * - pickupHouse, pickupBarangay, pickupMunicipality, pickupProvince
     * - pickupLat, pickupLng (optional)
     * - dropHouse, dropBarangay, dropMunicipality, dropProvince
     * - dropLat, dropLng (optional)
     * - vehicleType
     * - packageType
     * - packageDesc (optional)
     * - maxKg
     * - sizeLengthM, sizeWidthM, sizeHeightM
     * - distanceKm (optional)
     * - baseAmount, distanceAmount, doorToDoorAmount, totalAmount
     * - paymentMethod (default: paypal)
     */
    function createBooking($bookingData){
        $sql = "INSERT INTO bookings (
                    user_id, pickup_house, pickup_barangay, pickup_municipality, pickup_province,
                    pickup_lat, pickup_lng,
                    drop_house, drop_barangay, drop_municipality, drop_province,
                    drop_lat, drop_lng,
                    vehicle_type, package_type, package_desc, max_kg, size_length_m, size_width_m, size_height_m,
                    distance_km, base_amount, distance_amount, door_to_door_amount, total_amount,
                    payment_status, payment_method, tracking_status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'paid', ?, 'pending', NOW())";

        $params = [
            (int)$bookingData['userId'],
            $bookingData['pickupHouse'] ?? '',
            $bookingData['pickupBarangay'] ?? '',
            $bookingData['pickupMunicipality'],
            $bookingData['pickupProvince'],
            $bookingData['pickupLat'] ?? null,
            $bookingData['pickupLng'] ?? null,

            $bookingData['dropHouse'] ?? '',
            $bookingData['dropBarangay'] ?? '',
            $bookingData['dropMunicipality'],
            $bookingData['dropProvince'],
            $bookingData['dropLat'] ?? null,
            $bookingData['dropLng'] ?? null,

            $bookingData['vehicleType'],
            $bookingData['packageType'],
            $bookingData['packageDesc'] ?? null,
            (int)($bookingData['maxKg'] ?? 0),
            (float)($bookingData['sizeLengthM'] ?? 0),
            (float)($bookingData['sizeWidthM'] ?? 0),
            (float)($bookingData['sizeHeightM'] ?? 0),

            $bookingData['distanceKm'] ?? null,
            (float)$bookingData['baseAmount'],
            (float)$bookingData['distanceAmount'],
            (float)$bookingData['doorToDoorAmount'],
            (float)$bookingData['totalAmount'],

            $bookingData['paymentMethod'] ?? 'paypal',
        ];

        $stmt = $this->db->executeQuery($sql, $params);
        mysqli_stmt_close($stmt);

        return (int)$this->db->lastInsertId();
    }

    function getBookingsByUser($userId){
        $sql = "SELECT * FROM bookings WHERE user_id = ?  ORDER BY created_at DESC";
        $stmt = $this->db->executeQuery($sql, [(int)$userId]);
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
        
        $stmt = $this->db->executeQuery($sql, [(int)$bookingId]);
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

    /**
     * Assign a driver and mark booking as accepted.
     */
    function acceptBooking($bookingId, $driverId){
        $sql = "UPDATE bookings SET driver_id = ?, tracking_status = 'accepted', updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, [(int)$driverId, (int)$bookingId]);
        mysqli_stmt_close($stmt);
        return true;
    }

    /**
     * Mark booking as picked up.
     */
    function pickUpBooking($bookingId){
        $sql = "UPDATE bookings SET tracking_status = 'picked_up', updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, [(int)$bookingId]);
        mysqli_stmt_close($stmt);
        return true;
    }

    /**
     * Mark booking as in transit.
     */
    function markInTransit($bookingId){
        $sql = "UPDATE bookings SET tracking_status = 'in_transit', updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, [(int)$bookingId]);
        mysqli_stmt_close($stmt);
        return true;
    }

    /**
     * Mark booking as delivered.
     */
    function markDelivered($bookingId){
        $sql = "UPDATE bookings SET tracking_status = 'delivered', updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, [(int)$bookingId]);
        mysqli_stmt_close($stmt);
        return true;
    }

    /**
     * Cancel booking.
     */
    function cancelBooking($bookingId){
        $sql = "UPDATE bookings SET tracking_status = 'cancelled', updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, [(int)$bookingId]);
        mysqli_stmt_close($stmt);
        return true;
    }

    /**
     * Generic tracking status update with validation.
     */
    function setTrackingStatus($bookingId, $status){
        $allowed = ['pending','accepted','picked_up','in_transit','delivered','cancelled'];
        if (!in_array($status, $allowed, true)) {
            throw new InvalidArgumentException("Invalid tracking status: " . $status);
        }
        $sql = "UPDATE bookings SET tracking_status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, [$status, (int)$bookingId]);
        mysqli_stmt_close($stmt);
        return true;
    }

    /**
     * Convenience: assign a driver only.
     */
    function assignDriver($bookingId, $driverId){
        $sql = "UPDATE bookings SET driver_id = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, [(int)$driverId, (int)$bookingId]);
        mysqli_stmt_close($stmt);
        return true;
    }
}