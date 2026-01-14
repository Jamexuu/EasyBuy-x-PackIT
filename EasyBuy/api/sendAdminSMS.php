<?php
require_once 'classes/User.php';
require_once 'config.php';
require_once 'classes/Auth.php';

Auth::start();

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

$apiKey = $_ENV['IPROG_SMS_API_KEY'] ?? '';
$url = 'https://www.iprogsms.com/api/v1/sms_messages';

$phone = $data['phone_number'] ?? '';
$message = $data['message'] ?? '';

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

$result = json_decode($response, true);

if (isset($result['status']) && $result['status'] == 200) {
    echo json_encode($result);
} else {
    $error = $result['error'] ?? 'Failed to send message';
    echo json_encode(['status' => $result['status'] ?? 500, 'error' => $error]);
}
