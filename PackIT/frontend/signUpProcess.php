<?php
session_start(); // start session for success message

// Include Gmail functions
require_once __DIR__ . '/../api/gmail/sendMail.php';

// Path to users.json
$usersFile = __DIR__ . '/../api/data/users.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Collect all form inputs
    $firstName       = trim($_POST['firstName'] ?? '');
    $lastName        = trim($_POST['lastName'] ?? '');
    $contact         = trim($_POST['contact'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $password        = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    $houseNumber     = trim($_POST['houseNumber'] ?? '');
    $street          = trim($_POST['street'] ?? '');
    $subdivision     = trim($_POST['subdivision'] ?? '');
    $landmark        = trim($_POST['landmark'] ?? '');
    $province        = trim($_POST['province'] ?? '');
    $city            = trim($_POST['city'] ?? '');
    $barangay        = trim($_POST['barangay'] ?? '');
    $postal          = trim($_POST['postal'] ?? '');

    // 2. Basic validation
    $requiredFields = [
        'First Name' => $firstName,
        'Last Name'  => $lastName,
        'Contact'    => $contact,
        'Email'      => $email,
        'Password'   => $password,
        'Confirm Password' => $confirmPassword,
        'House/Unit' => $houseNumber,
        'Street'     => $street,
        'Province'   => $province,
        'City'       => $city,
        'Barangay'   => $barangay
    ];

    foreach ($requiredFields as $name => $value) {
        if (!$value) {
            die("Please fill in the required field: $name.");
        }
    }

    if ($password !== $confirmPassword) {
        die("Passwords do not match.");
    }

    // 3. Load existing users
    if (!file_exists($usersFile)) {
        file_put_contents($usersFile, json_encode([]));
    }
    $users = json_decode(file_get_contents($usersFile), true);

    // 4. Check if email already exists
    foreach ($users as $user) {
        if (strtolower($user['email']) === strtolower($email)) {
            die('Email already registered.');
        }
    }

    // 5. Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 6. Add new user with all fields
    $users[] = [
        'firstName'   => $firstName,
        'lastName'    => $lastName,
        'contact'     => $contact,
        'email'       => $email,
        'password'    => $hashedPassword,
        'houseNumber' => $houseNumber,
        'street'      => $street,
        'subdivision' => $subdivision,
        'landmark'    => $landmark,
        'province'    => $province,
        'city'        => $city,
        'barangay'    => $barangay,
        'postal'      => $postal,
        'created_at'  => date('Y-m-d H:i:s')
    ];

    // 7. Save back to JSON file
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

    // 8. Send welcome email
    $subject = "Welcome to PackIT, $firstName!";
    $html = "
        <h1>Hello $firstName!</h1>
        <p>Thank you for joining <b>PackIT Delivery Solutions</b>.</p>
        <p>You can now start shipping and tracking your deliveries!</p>
    ";

    try {
        sendMail($email, $subject, $html);
    } catch (Exception $e) {
        // Continue with registration even if email fails
    }

    // 9. Store a success message in session and redirect
    session_start(); // make sure session is started

    $_SESSION['success'] = "Welcome $firstName! Registration successful.";
    header("Location: ../frontend/dashboard.php");
    exit;
}
