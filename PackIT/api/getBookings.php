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
$db = new Database();

// Users can only see THEIR OWN bookings.
// Admin/Driver can see all bookings (and can filter by user_id if needed).
$userIdFilter = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$status = $_GET['tracking_status'] ?? null;

$sql = "SELECT
            b.*,
            d.first_name AS driver_first_name,
            d.last_name AS driver_last_name,
            d.contact_number AS driver_contact_number,
            d.vehicle_type AS driver_vehicle_type,
            d.license_plate AS driver_license_plate,
            d.is_available AS driver_is_available
        FROM bookings b
        LEFT JOIN drivers d ON d.id = b.driver_id
        WHERE 1=1";
$params = [];

if (is_admin_or_driver($user)) {
    if ($userIdFilter) {
        $sql .= " AND b.user_id = ?";
        $params[] = $userIdFilter;
    }
} else {
    // force to current user
    $sql .= " AND b.user_id = ?";
    $params[] = (int)$user['id'];
}

if ($status) {
    $sql .= " AND b.tracking_status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY b.created_at DESC";

$stmt = $db->executeQuery($sql, $params);
$rows = $db->fetch($stmt);

echo json_encode(['success' => true, 'data' => $rows]);