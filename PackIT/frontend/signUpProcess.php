<?php
session_start();

require_once __DIR__ . '/../api/classes/User.php';

// OPTIONAL email include (only if you really have a function for it)
// require_once __DIR__ . '/../api/gmail/sendMail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: signUp.php");
    exit();
}

// 1) Collect inputs
$firstName       = trim($_POST['firstName'] ?? '');
$lastName        = trim($_POST['lastName'] ?? '');
$contact         = trim($_POST['contact'] ?? '');
$email           = trim($_POST['email'] ?? '');
$password        = (string)($_POST['password'] ?? '');
$confirmPassword = (string)($_POST['confirm_password'] ?? '');

$houseNumber   = trim($_POST['houseNumber'] ?? '');
$street        = trim($_POST['street'] ?? '');
$subdivision   = trim($_POST['subdivision'] ?? '');
$landmark      = trim($_POST['landmark'] ?? '');
$province      = trim($_POST['province'] ?? '');
$city          = trim($_POST['city'] ?? '');
$barangay      = trim($_POST['barangay'] ?? '');
$postal        = trim($_POST['postal'] ?? '');

// 2) Validation
$errors = [];

$required = [
    'First Name' => $firstName,
    'Last Name' => $lastName,
    'Contact' => $contact,
    'Email' => $email,
    'Password' => $password,
    'Confirm Password' => $confirmPassword,
    'Province' => $province,
    'City' => $city,
    'Barangay' => $barangay,
];

foreach ($required as $label => $value) {
    if ($value === '') {
        $errors[] = "$label is required.";
    }
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

if ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match.";
}

if (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters.";
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(" ", $errors);
    header("Location: signUp.php");
    exit();
}

$user = new User();

// 3) Check email exists
if ($user->emailExists($email)) {
    $_SESSION['error'] = "Email already registered.";
    header("Location: signUp.php");
    exit();
}

// 4) Prepare user data (goes to users table)
$userData = [
    'firstName'     => $firstName,
    'lastName'      => $lastName,
    'email'         => $email,
    'password'      => $password,
    'contactNumber' => $contact,
];

// 5) Prepare address data (only used if your User::register inserts into addresses)
$addressData = [
    'houseNumber' => $houseNumber,
    'street'      => $street,
    'subdivision' => $subdivision,
    'landmark'    => $landmark,
    'barangay'    => $barangay,
    'city'        => $city,
    'province'    => $province,
    'postalCode'  => $postal,
];

// 6) Register user
try {
    $userId = $user->register($userData, $addressData, 'user');

    // 7) OPTIONAL: send welcome email only if the function exists
    /*
    if (function_exists('sendWelcomeEmail')) {
        try {
            sendWelcomeEmail($email, $firstName);
        } catch (Throwable $e) {
            error_log("Welcome email failed for $email: " . $e->getMessage());
        }
    }
    */

    $_SESSION['success'] = "Welcome $firstName! Registration successful.";
    header("Location: login.php");
    exit();
} catch (Throwable $e) {
    $_SESSION['error'] = "Registration failed: " . $e->getMessage();
    header("Location: signUp.php");
    exit();
}