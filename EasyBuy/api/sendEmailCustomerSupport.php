<?php


require_once 'classes/Mailer.php';
session_start();

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

try {

    $email = "thisis.acadpurposesonly@gmail.com";
    $subject = $data['subject'] ?? '';
    $userMessage = $data['message'] ?? '';
    $fromEmail = $data['email'] ?? '';

    $message = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Customer Support Message</title></head><body style="font-family: Arial, sans-serif; background: #f8f9fa; padding: 24px;">'
        .'<div style="max-width: 600px; margin: auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #eee; padding: 32px;">'
        .'<h2 style="color: #28a745; margin-bottom: 16px;">Customer Support Message</h2>'
        .'<p><strong>Subject:</strong> '.htmlspecialchars($subject).'</p>'
        .'<hr style="margin: 16px 0;">'
        .'<p style="font-size: 1.1em; color: #333;"><strong>Message:</strong><br>'.nl2br(htmlspecialchars($userMessage)).'</p>'
        .($fromEmail ? '<hr style="margin: 16px 0;"><p style="color: #888; font-size: 0.95em;">From: '.htmlspecialchars($fromEmail).'</p>' : '')
        .'</div></body></html>';

    if (empty($email)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email is required']);
        exit();
    }


    $mailer = new Mailer();
    $mailer->setSender("thisis.acadpurposesonly@gmail.com", "EasyBuy Customer");
    $mailer->addRecipient($email, "EasyBuy Support");
    $mailer->setSubject($subject);
    $mailer->isHTML(true);
    $mailer->setBody($message);
    $mailer->send();

    echo json_encode(['success' => true, 'message' => 'Email sent successfully.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}