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

$cacheKey = 'emails_cache';
$cacheTime = 300;

if (isset($_SESSION[$cacheKey]) && (time() - $_SESSION[$cacheKey]['time']) < $cacheTime) {
    echo json_encode(['success' => true, 'emails' => $_SESSION[$cacheKey]['data'], 'cached' => true]);
    exit();
}

try{
    $imap = new Imap();
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 20;

    $fetchLimit = $limit * 10;
    $emails = $imap->fetchEmails($fetchLimit);

    $unread = [];
    $read = [];
    foreach($emails as $email){
        $item = [
            'sender' => $imap->getMailSender($email),
            'email' => $imap->getEmailAddress($email),
            'subject' => $imap->getSubject($email),
            'date' => $imap->getEmailDate($email),
            'body' => $imap->getMailBody($email),
            'htmlBody' => $imap->getMailBodyHTML($email),
            'isUnread' => !$email->hasFlag('Seen')
        ];
        if ($item['isUnread']) {
            $unread[] = $item;
        } else {
            $read[] = $item;
        }
    }

    $unread = array_reverse($unread);
    $read = array_reverse($read);
    $allSorted = array_merge($unread, $read);

    $start = ($page - 1) * $limit;
    $pagedEmails = array_slice($allSorted, $start, $limit);

    $_SESSION[$cacheKey] = [
        'data' => $allSorted,
        'time' => time()
    ];

    echo json_encode([
        'success' => true,
        'emails' => $pagedEmails,
        'page' => $page,
        'limit' => $limit,
        'total' => count($allSorted)
    ]);
}catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}