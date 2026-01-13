<?php
require_once 'classes/Auth.php';
require_once 'classes/User.php';

Auth::start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email and password are required']);
    exit();
}

$user = new User();
$result = $user->login($email, $password);

if ($result) {
    $role = $result['role'] ?? 'user';
    Auth::login($result['id'], $result['email'], $result['first_name'], $role);
    echo json_encode(['success' => true, 'message' => 'Login successful', 'role' => $role]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
}