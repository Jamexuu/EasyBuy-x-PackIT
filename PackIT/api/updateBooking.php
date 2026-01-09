<?php

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/helpers/auth_api.php';
require_once __DIR__ . '/classes/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
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

$data = json_decode(file_get_contents('php://input'), true);
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON body']);
    exit();
}

$db = new Database();

// Load booking for authorization + rule checks
$stmt = $db->executeQuery("SELECT id, user_id, payment_status, tracking_status FROM bookings WHERE id = ?", [$id]);
$rows = $db->fetch($stmt);

if (empty($rows)) {
    http_response_code(404);
    echo json_encode(['error' => 'Booking not found']);
    exit();
}

$booking = $rows[0];

// Authorization:
// - Admin/Driver can update any booking
// - Normal user can only update their own booking (and we can restrict what they can update)
$isPrivileged = is_admin_or_driver($user);
$isOwner = ((int)$booking['user_id'] === (int)$user['id']);

if (!$isPrivileged && !$isOwner) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit();
}

// Business rule: once paid, do not allow changing payment_status away from 'paid'
if (($booking['payment_status'] === 'paid') && isset($data['payment_status']) && $data['payment_status'] !== 'paid') {
    http_response_code(400);
    echo json_encode(['error' => 'Payment status cannot be changed after payment is completed']);
    exit();
}

// Allowed updates:
// - Users: only payment_method (optional), maybe nothing else
// - Admin/Driver: tracking_status, driver_id, payment_status, payment_method
$allowed = $isPrivileged
    ? ['driver_id', 'tracking_status', 'payment_status', 'payment_method']
    : ['payment_method'];

$set = [];
$params = [];

foreach ($allowed as $key) {
    if (array_key_exists($key, $data)) {
        $set[] = "$key = ?";
        $params[] = $data[$key];
    }
}

if (empty($set)) {
    http_response_code(400);
    echo json_encode(['error' => 'No valid fields to update']);
    exit();
}

$params[] = $id;
$sql = "UPDATE bookings SET " . implode(', ', $set) . " WHERE id = ?";

$db->executeQuery($sql, $params);

echo json_encode(['success' => true, 'message' => 'Booking updated']);