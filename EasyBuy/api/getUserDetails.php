<?php
require_once 'classes/User.php';
require_once 'classes/Auth.php';

header('Content-Type: application/json');

// require authentication
Auth::start();
if (!Auth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// get user ID from session
$currentUser = Auth::getUser();
$userId = $currentUser['id'];

// fetch user details
$user = new User();
$data = $user->getUserDetails($userId);

if ($data) {
    echo json_encode($data);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
}