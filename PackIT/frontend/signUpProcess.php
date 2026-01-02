<?php
session_start();

// Include the User class
require_once __DIR__ .'/../api/classes/User.php';

// Include Gmail functions
require_once __DIR__ .'/../api/gmail/sendMail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1.Collect all form inputs
    $firstName       = trim($_POST['firstName'] ?? '');
    $lastName        = trim($_POST['lastName'] ??  '');
    $contact         = trim($_POST['contact'] ?? '');
    $email           = trim($_POST['email'] ??  '');
    $password        = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ??  '');
    $houseNumber     = trim($_POST['houseNumber'] ?? '');
    $street          = trim($_POST['street'] ?? '');
    $subdivision     = trim($_POST['subdivision'] ?? '');
    $landmark        = trim($_POST['landmark'] ??  '');
    $province        = trim($_POST['province'] ?? '');
    $city            = trim($_POST['city'] ??  '');
    $barangay        = trim($_POST['barangay'] ?? '');
    $postal          = trim($_POST['postal'] ?? '');

    // 2.Basic validation
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

    // 3.Initialize User class
    $user = new User();

    // 4.Check if email already exists
    if ($user->emailExists($email)) {
        die('Email already registered.');
    }

    // 5.Prepare user data
    $userData = [
        'firstName'     => $firstName,
        'lastName'      => $lastName,
        'email'         => $email,
        'password'      => $password,
        'contactNumber' => $contact
    ];

    // 6.Prepare address data
    $addressData = [
        'houseNumber'  => $houseNumber,
        'street'       => $street,
        'subdivision'  => $subdivision,
        'landmark'     => $landmark,
        'barangay'     => $barangay,
        'city'         => $city,
        'province'     => $province,
        'postalCode'   => $postal
    ];

    // 7.Register user (inserts into database)
    try {
        $userId = $user->register($userData, $addressData, 'user');
        
        // 8.Send welcome email
        try {
            sendWelcomeEmail($email, $firstName);
        } catch (Exception $e) {
            // Log error but continue with registration
            error_log("Welcome email failed for $email: " .$e->getMessage());
        }

        // 9.Store success message and redirect
        $_SESSION['success'] = "Welcome $firstName!  Registration successful.Check your email! ";
        header("Location: login.php");
        exit;

    } catch (Exception $e) {
        die("Registration failed:  " .$e->getMessage());
    }
}
?>