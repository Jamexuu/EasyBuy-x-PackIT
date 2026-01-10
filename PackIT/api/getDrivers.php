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

/**
 * Your schema uses a separate `drivers` table:
 * drivers(id, first_name, last_name, email, password, contact_number, vehicle_type, license_plate, is_available, ...)
 *
 * IMPORTANT: Do not return password.
 */

// Optional filter: ?is_available=1 or 0
$isAvailable = isset($_GET['is_available']) ? (int)$_GET['is_available'] : null;

$sql = "SELECT
            id,
            first_name,
            last_name,
            email,
            contact_number,
            vehicle_type,
            license_plate,
            is_available,
            created_at
        FROM drivers
        WHERE 1=1";
$params = [];

if ($isAvailable !== null) {
    $sql .= " AND is_available = ?";
    $params[] = $isAvailable;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $db->executeQuery($sql, $params);
$rows = $db->fetch($stmt);

echo json_encode(['success' => true, 'data' => $rows]);