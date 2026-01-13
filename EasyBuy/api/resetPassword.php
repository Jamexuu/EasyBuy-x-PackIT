<?php
require_once 'classes/User.php';
require_once 'classes/Auth.php';

Auth::start();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$email = $_SESSION['forgot_email'] ?? '';
$newPassword = $data['new_password'] ?? '';

if (!$email) {
    echo json_encode(['success' => false, 'error' => 'Email required']);
    exit;
}

if (!$newPassword) {
    echo json_encode(['success' => false, 'error' => 'New password required']);
    exit;
}

$user = new User();
$updateResult = $user->updatePassword($email, $newPassword);

if ($updateResult) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update password']);
}