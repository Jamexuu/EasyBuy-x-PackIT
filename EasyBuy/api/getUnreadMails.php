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

$user = Auth::requireAdmin();
try{
    $imap = new Imap();
    $emails = $imap->fetchUnreadEmails(20);

    $unreadEmailList = [];

    foreach($emails as $email){
        if(!$email->hasFlag('Seen')){
            $unreadEmailList[] = [
                'sender' => $imap->getMailSender($email),
                'email' => $imap->getEmailAddress($email),
                'subject' => $imap->getSubject($email),
                'date' => $imap->getEmailDate($email),
                'body' => $imap->getMailBody($email),
                'htmlBody' => $imap->getMailBodyHTML($email),
                'isUnread' => true
            ];
        }
    }

    echo json_encode(['success' => true, 'emails' => $unreadEmailList]);
}catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
