<?php

require 'classes/Imap.php';
require 'classes/Auth.php';
Auth::start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

Auth::requireAdmin();

// cache for 5 minutes
$cacheKey = 'emails_cache';
$cacheTime = 300;

if (isset($_SESSION[$cacheKey]) && (time() - $_SESSION[$cacheKey]['time']) < $cacheTime) {
    echo json_encode(['success' => true, 'emails' => $_SESSION[$cacheKey]['data'], 'cached' => true]);
    exit();
}

try{
    $imap = new Imap();
    $emails = $imap->fetchEmails(20);

    $emailList = [];

    foreach($emails as $email){
        $emailList[] = [
            'sender' => $imap->getMailSender($email),
            'email' => $imap->getEmailAddress($email),
            'subject' => $imap->getSubject($email),
            'date' => $imap->getEmailDate($email),
            'body' => $imap->getMailBody($email),
            'htmlBody' => $imap->getMailBodyHTML($email),
            'isUnread' => !$email->hasFlag('Seen')
        ];
    }

    // Reverse array for LIFO (Last In First Out) - newest emails first
    $emailList = array_reverse($emailList);

    // store in cache
    $_SESSION[$cacheKey] = [
        'data' => $emailList,
        'time' => time()
    ];

    echo json_encode(['success' => true, 'emails' => $emailList]);
}catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}