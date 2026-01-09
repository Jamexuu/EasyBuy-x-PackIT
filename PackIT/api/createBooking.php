<?php

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/helpers/auth_api.php';
require_once __DIR__ . '/classes/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

$user = require_api_auth();

$data = json_decode(file_get_contents('php://input'), true);
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON body']);
    exit();
}

// IMPORTANT RULE: user_id comes from session (prevents creating bookings for other users)
$user_id = (int)$user['id'];

// Required fields (minimum)
$required = [
    'pickup_municipality', 'pickup_province',
    'drop_municipality', 'drop_province',
    'vehicle_type', 'package_type',
    'total_amount'
];

$missing = [];
foreach ($required as $key) {
    if (!isset($data[$key]) || $data[$key] === '') $missing[] = $key;
}
if (!empty($missing)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields', 'missing' => $missing]);
    exit();
}

$db = new Database();

// Optional fields with defaults
$driver_id = null; // user should not assign driver; keep null on create

$pickup_contact_name = $data['pickup_contact_name'] ?? null;
$pickup_contact_number = $data['pickup_contact_number'] ?? null;
$pickup_house = $data['pickup_house'] ?? null;
$pickup_barangay = $data['pickup_barangay'] ?? null;
$pickup_municipality = $data['pickup_municipality'];
$pickup_province = $data['pickup_province'];

$drop_house = $data['drop_house'] ?? null;
$drop_barangay = $data['drop_barangay'] ?? null;
$drop_municipality = $data['drop_municipality'];
$drop_province = $data['drop_province'];
$drop_contact_name = $data['drop_contact_name'] ?? null;
$drop_contact_number = $data['drop_contact_number'] ?? null;

$vehicle_type = $data['vehicle_type'];
$package_type = $data['package_type'];
$package_desc = $data['package_desc'] ?? null;

$package_quantity = isset($data['package_quantity']) ? (int)$data['package_quantity'] : 1;
$max_kg = isset($data['max_kg']) ? (int)$data['max_kg'] : 0;

$size_length_m = isset($data['size_length_m']) ? (float)$data['size_length_m'] : 0;
$size_width_m  = isset($data['size_width_m']) ? (float)$data['size_width_m'] : 0;
$size_height_m = isset($data['size_height_m']) ? (float)$data['size_height_m'] : 0;

$distance_km = isset($data['distance_km']) ? (float)$data['distance_km'] : null;

$base_amount = isset($data['base_amount']) ? (float)$data['base_amount'] : 0;
$distance_amount = isset($data['distance_amount']) ? (float)$data['distance_amount'] : 0;
$door_to_door_amount = isset($data['door_to_door_amount']) ? (float)$data['door_to_door_amount'] : 0;

$total_amount = (float)$data['total_amount'];

// Always pending on creation
$payment_status = 'pending';
$payment_method = $data['payment_method'] ?? null;
$tracking_status = 'pending';

$sql = "INSERT INTO bookings (
    user_id, driver_id,
    pickup_contact_name, pickup_contact_number, pickup_house, pickup_barangay, pickup_municipality, pickup_province,
    drop_house, drop_barangay, drop_municipality, drop_province, drop_contact_name, drop_contact_number,
    vehicle_type, package_type, package_desc,
    package_quantity, max_kg,
    size_length_m, size_width_m, size_height_m,
    distance_km, base_amount, distance_amount, door_to_door_amount,
    total_amount, payment_status, payment_method, tracking_status
) VALUES (
    ?, ?,
    ?, ?, ?, ?, ?, ?,
    ?, ?, ?, ?, ?, ?,
    ?, ?, ?,
    ?, ?,
    ?, ?, ?,
    ?, ?, ?, ?,
    ?, ?, ?, ?
)";

$params = [
    $user_id, $driver_id,
    $pickup_contact_name, $pickup_contact_number, $pickup_house, $pickup_barangay, $pickup_municipality, $pickup_province,
    $drop_house, $drop_barangay, $drop_municipality, $drop_province, $drop_contact_name, $drop_contact_number,
    $vehicle_type, $package_type, $package_desc,
    $package_quantity, $max_kg,
    $size_length_m, $size_width_m, $size_height_m,
    $distance_km, $base_amount, $distance_amount, $door_to_door_amount,
    $total_amount, $payment_status, $payment_method, $tracking_status
];

$db->executeQuery($sql, $params);
$newId = $db->lastInsertId();

http_response_code(201);
echo json_encode(['success' => true, 'message' => 'Booking created', 'booking_id' => $newId]);