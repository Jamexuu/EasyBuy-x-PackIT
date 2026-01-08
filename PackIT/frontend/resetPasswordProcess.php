<?php
session_start();
require_once __DIR__ . '/../api/classes/User.php';
require_once __DIR__ . '/../api/classes/PasswordHelper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Use session user id (set after OTP verification)
$userId = $_SESSION['password_reset_user_id'] ?? null;
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (!$userId) {
    $_SESSION['fp_error'] = 'Session expired or invalid. Please request a new code.';
    header('Location: forgotPassword.php');
    exit;
}

if ($password === '' || $confirm === '') {
    $_SESSION['fp_error'] = 'All fields are required.';
    header('Location: resetPassword.php');
    exit;
}

if ($password !== $confirm) {
    $_SESSION['fp_error'] = 'Passwords do not match.';
    header('Location: resetPassword.php');
    exit;
}

// Validate password complexity (at least 8 chars with letters and numbers)
$pwCheck = PasswordHelper::validate($password);
if ($pwCheck !== true) {
    $_SESSION['fp_error'] = $pwCheck;
    header('Location: resetPassword.php');
    exit;
}

$user = new User();
$ok = $user->resetPasswordForUser($userId, $password);

if ($ok) {
    // clear session marker
    unset($_SESSION['password_reset_user_id']);
    $_SESSION['success'] = 'Password updated — you may now sign in.';
    header('Location: login.php');
    exit;
} else {
    $_SESSION['fp_error'] = 'Could not reset password. Try again.';
    header('Location: forgotPassword.php');
    exit;
}