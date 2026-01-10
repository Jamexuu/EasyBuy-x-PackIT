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

$bookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
if ($bookingId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'booking_id is required']);
    exit();
}

$db = new Database();

// Load booking for authorization + driver_id
$stmt = $db->executeQuery("SELECT id, user_id, driver_id FROM bookings WHERE id = ?", [$bookingId]);
$rows = $db->fetch($stmt);

if (empty($rows)) {
    http_response_code(404);
    echo json_encode(['error' => 'Booking not found']);
    exit();
}

$booking = $rows[0];

// Authorization: owner OR admin/driver
if (!is_admin_or_driver($user) && (int)$booking['user_id'] !== (int)$user['id']) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit();
}

$driverId = (int)($booking['driver_id'] ?? 0);
if ($driverId <= 0) {
    http_response_code(404);
    echo json_encode(['error' => 'No driver assigned yet']);
    exit();
}

// Return only necessary + safe fields (NEVER return password)
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

if (empty($driverRows)) {
    http_response_code(404);
    echo json_encode(['error' => 'Driver not found']);
    exit();
}

echo json_encode(['success' => true, 'data' => $driverRows[0]]);