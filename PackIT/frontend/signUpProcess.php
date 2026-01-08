<?php
session_start();

require_once __DIR__ . '/../api/classes/User.php';

// OPTIONAL: include Gmail helper if available (sendMail.php)
$gmailSendAvailable = false;
if (file_exists(__DIR__ . '/../api/gmail/sendMail.php')) {
    require_once __DIR__ . '/../api/gmail/sendMail.php';
    $gmailSendAvailable = function_exists('sendMail');
}

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

    // 7) Send welcome email (non-blocking)
    try {
        $subject = "Welcome to PackIT";
        $html = '
            <div style="font-family: Arial, sans-serif; line-height:1.5; color:#111;">
                <h2 style="margin:0 0 10px;">Welcome, ' . htmlspecialchars($firstName) . '!</h2>
                <p>Your PackIT account has been created. You can now log in and start using our services.</p>
                <p style="margin-top:12px;">If you did not create this account, please contact support.</p>
                <div style="margin-top:18px; padding:12px; background:#f8f4d6; border-radius:8px;">
                    <strong>Account email:</strong> ' . htmlspecialchars($email) . '
                </div>
            </div>
        ';

        if ($gmailSendAvailable) {
            // sendMail expected signature: sendMail($to, $subject, $htmlBody)
            $sent = sendMail($email, $subject, $html);
            if (!$sent) {
                error_log("PackIT: sendMail() returned falsy when sending welcome email to $email");
            }
        } else {
            // Fallback to PHP mail(); convert to plain text for mail()
            $plain = strip_tags($html);
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: PackIT <no-reply@packit.local>\r\n";
            $sent = mail($email, $subject, $html, $headers);
            if (!$sent) {
                error_log("PackIT: mail() failed when sending welcome email to $email");
            }
        }
    } catch (Throwable $e) {
        // Do not interrupt registration on email errors; log for debugging.
        error_log("PackIT: welcome email error for $email - " . $e->getMessage());
    }

    $_SESSION['success'] = "Welcome $firstName! Registration successful.";
    header("Location: login.php");
    exit();
} catch (Throwable $e) {
    $_SESSION['error'] = "Registration failed: " . $e->getMessage();
    header("Location: signUp.php");
    exit();
}