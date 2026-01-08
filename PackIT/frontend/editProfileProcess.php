<?php
session_start();
require_once __DIR__ . '/../api/classes/User.php';

// Check login
if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
    exit;
}

// CSRF Check
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = 'Invalid request (CSRF token mismatch).';
    header('Location: profile.php');
    exit;
}

// Collect Inputs
$firstName   = trim($_POST['firstName'] ?? '');
$lastName    = trim($_POST['lastName'] ?? '');
$contact     = trim($_POST['contact'] ?? '');

$houseNumber = trim($_POST['houseNumber'] ?? '');
$street      = trim($_POST['street'] ?? '');
$subdivision = trim($_POST['subdivision'] ?? '');
$landmark    = trim($_POST['landmark'] ?? '');
$barangay    = trim($_POST['barangay'] ?? '');
$city        = trim($_POST['city'] ?? '');
$province    = trim($_POST['province'] ?? '');
$postal      = trim($_POST['postal'] ?? '');

if ($firstName === '' || $lastName === '' || $contact === '') {
    $_SESSION['error'] = 'Name and Contact Number are required.';
    header('Location: profile.php');
    exit;
}

$userData = [
    'firstName'     => $firstName,
    'lastName'      => $lastName,
    'contactNumber' => $contact
];

$addressData = [
    'houseNumber' => $houseNumber,
    'street'      => $street,
    'subdivision' => $subdivision,
    'landmark'    => $landmark,
    'barangay'    => $barangay,
    'city'        => $city,
    'province'    => $province,
    'postalCode'  => $postal
];

$userObj = new User();
try {
    $userObj->updateProfile((int)$_SESSION['user']['id'], $userData, $addressData);
    $_SESSION['success'] = 'Profile updated successfully.';
} catch (Exception $e) {
    $_SESSION['error'] = 'Update failed: ' . $e->getMessage();
}

header('Location: profile.php');
exit;