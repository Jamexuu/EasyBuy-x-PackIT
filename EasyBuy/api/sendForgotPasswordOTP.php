<?php
require_once 'classes/User.php';
require_once 'config.php';
require_once 'classes/Auth.php';

Auth::start();

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';

if (!$email) {
    echo json_encode(['success' => false, 'error' => 'Email required']);
    exit;
}

$user = new User();

if ($user->findByEmail($email) === false) {
    echo json_encode(['success' => false, 'error' => 'Email not found']);
    exit;
}

$phone = $user->getPhoneNumber($email);

if (!$phone) {
    echo json_encode(['success' => false, 'error' => 'No phone found for this email']);
    exit;
}

$otp = rand(100000, 999999);
$_SESSION['forgot_otp'] = $otp;
$_SESSION['forgot_email'] = $email;

$message = "Here's your OTP $otp";
$apiKey = $_ENV['IPROG_SMS_API_KEY'] ?? '';
$url = 'https://www.iprogsms.com/api/v1/otp/send_otp';
$data = [
    'api_token' => $apiKey,
    'message' => $message,
    'phone_number' => $phone,
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);
$response = curl_exec($ch);
curl_close($ch);

echo json_encode(['success' => true, 'message' => 'OTP sent']);