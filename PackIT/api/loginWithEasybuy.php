<?php
require_once 'classes/Auth.php';
require_once 'classes/User.php';
require_once 'config.php';
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

$packitLoginUrl = "http://192.168.254.104/EasyBuy-x-PackIT/PackIT/api/login.php";

$ch = curl_init($packitLoginUrl);
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
    echo json_encode(['error' => 'Invalid PackIT credentials']);
    exit();
}

$packitResponse = json_decode($response, true);

if (!$packitResponse || !isset($packitResponse['success']) || !$packitResponse['success']) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid PackIT credentials']);
    exit();
}

// PackIT login successful, now create/link user in EasyBuy
$db = new Database();

// Check if user exists in EasyBuy
$query = "SELECT * FROM users WHERE email = ?";
$result = $db->executeQuery($query, [$email]);
$existingUser = $db->fetch($result);

if (empty($existingUser)) {
    // User doesn't exist in EasyBuy, fetch data from PackIT
    $packitUserUrl = "http://192.168.254.104/EasyBuy-x-PackIT/PackIT/api/getUserDataPublic.php?email=" . urlencode($email);
    
    $ch = curl_init($packitUserUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $userDataResponse = curl_exec($ch);
    $userHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($userHttpCode !== 200) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch user data from PackIT']);
        exit();
    }
    
    $packitUserData = json_decode($userDataResponse, true);
    
    if (!$packitUserData || !$packitUserData['success'] || !isset($packitUserData['data'])) {
        http_response_code(500);
        echo json_encode(['error' => 'Invalid user data from PackIT']);
        exit();
    }
    
    $packitData = $packitUserData['data'];
    
    // Prepare user data for EasyBuy registration
    $userData = [
        'firstName' => $packitData['first_name'],
        'lastName' => $packitData['last_name'],
        'email' => $packitData['email'],
        'password' => bin2hex(random_bytes(16)), // Random password
        'contactNumber' => $packitData['contact_number']
    ];
    
    // Map PackIT address fields to EasyBuy format
    $addressData = [
        'houseNumber' => $packitData['house_number'] ?? '',
        'street' => $packitData['street'] ?? '',
        'lot' => $packitData['subdivision'] ?? '', // subdivision → lot
        'block' => $packitData['landmark'] ?? '', // landmark → block
        'barangay' => $packitData['barangay'] ?? '',
        'city' => $packitData['city'] ?? '',
        'province' => $packitData['province'] ?? '',
        'postalCode' => $packitData['postal_code'] ?? ''
    ];
    
    // Create user using User class register method
    $userClass = new User();
    $userClass->register($userData, $addressData);
    
    // Get the newly created user ID
    $query = "SELECT id FROM users WHERE email = ?";
    $result = $db->executeQuery($query, [$packitData['email']]);
    $newUser = $db->fetch($result);
    $userId = $newUser[0]['id'];
    
    // Log in the new user
    Auth::login($userId, $packitData['email'], $packitData['first_name']);
} else {
    // User exists, log them in
    $user = $existingUser[0];
    Auth::login($user['id'], $user['email'], $user['first_name']);
}

echo json_encode(['success' => true, 'message' => 'Login successful with PackIT']);
