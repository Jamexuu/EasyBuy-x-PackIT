<?php
require_once __DIR__ . '/sendMail.php';

$client = new Google_Client();
$client->setApplicationName('PackIT Mailer');
$client->setScopes(Google_Service_Gmail::GMAIL_SEND);
$client->setAuthConfig(__DIR__ . '/client.json');
$client->setAccessType('offline');
$client->setPrompt('select_account consent');
$client->setRedirectUri('http://localhost/EasyBuy-x-PackIT/PackIT/api/gmail/authorize.php');

// 👇 HANDLE GOOGLE CALLBACK
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        echo "Error fetching token:";
        print_r($token);
        exit;
    }

    file_put_contents(__DIR__ . '/token.json', json_encode($token));
    echo "✅ Gmail authorized successfully. You can now close this page.";
    exit;
}

// 👇 FIRST TIME: SHOW AUTH LINK
$authUrl = $client->createAuthUrl();
echo "<a href='$authUrl'>Authorize PackIT Gmail</a>";
