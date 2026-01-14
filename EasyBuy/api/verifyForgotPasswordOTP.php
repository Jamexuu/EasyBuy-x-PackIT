<?php
require_once 'classes/Auth.php';

Auth::start();

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

$otp = $data['otp'] ?? '';

if (!$otp) {
    echo json_encode(['success' => false, 'error' => 'OTP is required']);
    exit;
}

if (!isset($_SESSION['forgot_otp']) || !isset($_SESSION['forgot_email'])) {
    echo json_encode(['success' => false, 'error' => 'Session expired. Please restart the password reset process.']);
    exit;
}

if ($otp == $_SESSION['forgot_otp']) {
    // Mark OTP as verified but keep email for password reset
    $_SESSION['otp_verified'] = true;
    unset($_SESSION['forgot_otp']); // Remove OTP after verification
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid OTP']);
}