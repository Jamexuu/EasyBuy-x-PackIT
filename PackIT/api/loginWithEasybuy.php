<?php
require_once 'classes/Auth.php';
require_once 'classes/User.php';
require_once 'classes/Database.php';

Auth::start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email and password are required']);
    exit();
}

$easybuyIP = '192.168.254.104' ?? 'localhost';
$easybuyLoginUrl = "http://$easybuyIP/EasyBuy-x-PackIT/EasyBuy/api/login.php";

$ch = curl_init($easybuyLoginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => $email,
    'password' => $password
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects
curl_setopt($ch, CURLOPT_COOKIE, ''); // Don't send cookies
curl_setopt($ch, CURLOPT_COOKIESESSION, true); // Don't store cookies

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid EasyBuy credentials']);
    exit();
}

$easybuyResponse = json_decode($response, true);

if (!$easybuyResponse || !isset($easybuyResponse['success']) || !$easybuyResponse['success']) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid EasyBuy credentials']);
    exit();
}

// EasyBuy login successful, now create/link user in PackIT
$db = new Database();

// Check if user exists in PackIT
$query = "SELECT * FROM users WHERE email = ?";
$result = $db->executeQuery($query, [$email]);
$existingUser = $db->fetch($result);

if (empty($existingUser)) {
    // User doesn't exist in PackIT, fetch data from EasyBuy
    $easybuyUserUrl = "http://$easybuyIP/EasyBuy-x-PackIT/EasyBuy/api/getUserDataPublic.php?email=" . urlencode($email);
    
    $ch = curl_init($easybuyUserUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $userDataResponse = curl_exec($ch);
    $userHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($userHttpCode !== 200) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch user data from EasyBuy']);
        exit();
    }
    
    $easybuyUserData = json_decode($userDataResponse, true);
    
    if (!$easybuyUserData || !$easybuyUserData['success'] || !isset($easybuyUserData['data'])) {
        http_response_code(500);
        echo json_encode(['error' => 'Invalid user data from EasyBuy']);
        exit();
    }
    
    $easybuyData = $easybuyUserData['data'];
    
    // Prepare user data for PackIT registration
    $userData = [
        'firstName' => $easybuyData['first_name'],
        'lastName' => $easybuyData['last_name'],
        'email' => $easybuyData['email'],
        'password' => bin2hex(random_bytes(16)), // Random password
        'contactNumber' => $easybuyData['contact_number']
    ];
    
    // Map EasyBuy address fields to PackIT format
    $addressData = [
        'houseNumber' => $easybuyData['house_number'] ?? '',
        'street' => $easybuyData['street'] ?? '',
        'subdivision' => $easybuyData['lot'] ?? '', // lot → subdivision
        'landmark' => $easybuyData['block'] ?? '', // block → landmark
        'barangay' => $easybuyData['barangay'] ?? '',
        'city' => $easybuyData['city'] ?? '',
        'province' => $easybuyData['province'] ?? '',
        'postalCode' => $easybuyData['postal_code'] ?? ''
    ];
    
    // Create user using User class register method
    $userClass = new User();
    $userId = $userClass->register($userData, $addressData);
    
    // Log in the new user
    $fullName = $easybuyData['first_name'] . ' ' . $easybuyData['last_name'];
    Auth::login($userId, $easybuyData['email'], $fullName, 'user');
} else {
    // User exists, log them in
    $user = $existingUser[0];
    $fullName = $user['first_name'] . ' ' . $user['last_name'];
    Auth::login($user['id'], $user['email'], $fullName, $user['role']);
}

echo json_encode(['success' => true, 'message' => 'Login successful with EasyBuy']);
