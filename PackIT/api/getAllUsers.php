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

// Admin-only
if (($user['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit();
}

$db = new Database();

// Optional filters
$role = isset($_GET['role']) ? $_GET['role'] : null; // user|admin|driver
$q = isset($_GET['q']) ? trim($_GET['q']) : null;    // search by name/email/contact

$sql = "SELECT
            id,
            first_name,
            last_name,
            email,
            contact_number,
            profile_image,
            role,
            created_at
        FROM users
        WHERE 1=1";
$params = [];

if ($role) {
    $sql .= " AND role = ?";
    $params[] = $role;
}

if ($q !== null && $q !== '') {
    $sql .= " AND (
        first_name LIKE ?
        OR last_name LIKE ?
        OR email LIKE ?
        OR contact_number LIKE ?
    )";
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like, $like);
}

$sql .= " ORDER BY created_at DESC";

$stmt = $db->executeQuery($sql, $params);
$rows = $db->fetch($stmt);

echo json_encode(['success' => true, 'data' => $rows]);