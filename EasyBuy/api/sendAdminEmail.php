<?php
require_once 'classes/Mailer.php';
require_once 'classes/Auth.php';

$auth = new Auth;

$auth->start();
$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

header('Content-Type: application/json');

try {
    $email = $_POST['email'] ?? '';
    $name = $_POST['name'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $attachment = $_FILES['attachment'] ?? null;

    if (empty($email)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email is required']);
        exit();
    }

    $mailer = new Mailer();
    $mailer->setSender("thisis.acadpurposesonly@gmail.com", "EasyBuy Admin");
    $mailer->addRecipient($email, $name);

    if ($attachment && $attachment['error'] !== UPLOAD_ERR_NO_FILE) {
        $mailer->addAttachment($attachment);
    }
    
    $mailer->setSubject($subject);
    $mailer->setBody($message);
    $mailer->send();

    echo json_encode(['success' => true, 'message' => 'Email sent successfully.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

