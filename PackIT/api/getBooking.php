<?php

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/helpers/auth_api.php';
require_once __DIR__ . '/classes/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

$user = require_api_auth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Booking id is required']);
    exit();
}

$db = new Database();

$stmt = $db->executeQuery("SELECT * FROM bookings WHERE id = ?", [$id]);
$rows = $db->fetch($stmt);

if (empty($rows)) {
    http_response_code(404);
    echo json_encode(['error' => 'Booking not found']);
    exit();
}

$booking = $rows[0];

// Authorization: user can only view own booking (unless admin/driver)
if (!is_admin_or_driver($user) && (int)$booking['user_id'] !== (int)$user['id']) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit();
}

// Attach driver info (safe fields only)
$driver = null;
$driverId = isset($booking['driver_id']) ? (int)$booking['driver_id'] : 0;

if ($driverId > 0) {
    $stmt = $db->executeQuery(
        "SELECT
            id,
            first_name,
            last_name,
            contact_number,
            vehicle_type,
            license_plate,
            is_available
         FROM drivers
         WHERE id = ?",
        [$driverId]
    );
    $driverRows = $db->fetch($stmt);
    if (!empty($driverRows)) {
        $driver = $driverRows[0];
    }
}

echo json_encode([
    'success' => true,
    'data' => [
        'booking' => $booking,
        'driver' => $driver
    ]
]);