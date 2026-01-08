<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: changePassword.php');
    exit;
}

// require User class
require_once __DIR__ . '/../api/classes/User.php';

// CSRF
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = 'Invalid request (CSRF).';
    header('Location: changePassword.php');
    exit;
}

if (!isset($_SESSION['user']['id'])) {
    $_SESSION['error'] = 'Not authenticated.';
    header('Location: login.php');
    exit;
}

$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

// Basic validation
if ($current === '' || $new === '' || $confirm === '') {
    $_SESSION['error'] = 'Please fill in all fields.';
    header('Location: changePassword.php');
    exit;
}

if ($new !== $confirm) {
    $_SESSION['error'] = 'New passwords do not match.';
    header('Location: changePassword.php');
    exit;
}

if (strlen($new) < 6) {
    $_SESSION['error'] = 'New password must be at least 6 characters.';
    header('Location: changePassword.php');
    exit;
}

$userObj = new User();
$userId = (int)$_SESSION['user']['id'];

$result = $userObj->changePassword($userId, $current, $new);

if ($result === true) {
    // Success: set message and redirect to profile
    $_SESSION['success'] = 'Password changed successfully.';
    // For safety regenerate session id
    session_regenerate_id(true);
    header('Location: profile.php');
    exit;
} else {
    // changePassword returns a string message on failure
    $_SESSION['error'] = is_string($result) ? $result : 'Failed to change password.';
    header('Location: changePassword.php');
    exit;
}