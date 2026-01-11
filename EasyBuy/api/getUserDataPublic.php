<?php
require_once __DIR__ . '/classes/Database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

$email = $_GET['email'] ?? '';

if (empty($email)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email is required']);
    exit();
}

$db = new Database();

// Get user with address
$query = "SELECT u.id, u.first_name, u.last_name, u.email, u.contact_number,
                 a.house_number, a.street, a.lot, a.block, 
                 a.barangay, a.city, a.province, a.postal_code
          FROM users u
          LEFT JOIN addresses a ON u.id = a.user_id
          WHERE u.email = ?";

$result = $db->executeQuery($query, [$email]);
$userData = $db->fetch($result);

if (empty($userData)) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    exit();
}

$user = $userData[0];

echo json_encode([
    'success' => true,
    'data' => $user
]);
