<?php
session_start();
require_once __DIR__ . '/../api/classes/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$token = trim($_POST['token'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($token === '' || $password === '' || $confirm === '') {
    $_SESSION['fp_error'] = 'All fields are required.';
    header('Location: resetPassword.php?token=' . urlencode($token));
    exit;
}

if ($password !== $confirm) {
    $_SESSION['fp_error'] = 'Passwords do not match.';
    header('Location: resetPassword.php?token=' . urlencode($token));
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['fp_error'] = 'Password must be at least 6 characters.';
    header('Location: resetPassword.php?token=' . urlencode($token));
    exit;
}

$user = new User();
$ok = $user->resetPasswordByToken($token, $password);

if ($ok) {
    $_SESSION['success'] = 'Password updated — you may now sign in.';
    header('Location: login.php');
    exit;
} else {
    $_SESSION['fp_error'] = 'Reset link invalid or expired.';
    header('Location: forgotPassword.php');
    exit;
}