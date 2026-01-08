<?php
session_start();
require_once __DIR__ . '/../api/classes/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgotPassword.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$otp = trim($_POST['otp'] ?? '');

if ($email === '' || $otp === '') {
    $_SESSION['fp_error'] = 'All fields are required.';
    header('Location: verifyOTP.php?email=' . urlencode($email));
    exit;
}

$user = new User();
$entry = $user->verifyPasswordResetOTP($email, $otp);

if (!$entry) {
    $_SESSION['fp_error'] = 'Invalid or expired code.';
    header('Location: verifyOTP.php?email=' . urlencode($email));
    exit;
}

// OTP valid — set session flag to allow password reset. Use user_id from the password_resets row.
$_SESSION['password_reset_user_id'] = $entry['user_id'];
// Optionally store a short-lived marker so resetPassword.php doesn't need token
header('Location: resetPassword.php');
exit;