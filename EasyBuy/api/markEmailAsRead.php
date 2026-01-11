<?php

require 'classes/Imap.php';
require 'classes/Auth.php';

Auth::start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

Auth::requireAdmin();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['emailIndex'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Email index is required']);
        exit();
    }
    
    $emailIndex = (int)$data['emailIndex'];
    
    $imap = new Imap();
    $emails = $imap->fetchEmails(100);
    
    $emails = array_reverse($emails->toArray());
    
    if (!isset($emails[$emailIndex])) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Email not found']);
        exit();
    }
    
    $email = $emails[$emailIndex];
    $imap->markAsRead($email);
    
    if (isset($_SESSION['emails_cache'])) {
        unset($_SESSION['emails_cache']);
    }
    
    echo json_encode(['success' => true, 'message' => 'Email marked as read']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
