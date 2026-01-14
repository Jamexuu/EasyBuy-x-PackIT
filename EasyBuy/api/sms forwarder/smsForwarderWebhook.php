<?php

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    $data = [];
}

$data['id'] = uniqid();
$data['sentStamp'] = date('Y-m-d H:i:s');
$data['receivedStamp'] = date('Y-m-d H:i:s');

$messages = [];
if (file_exists('messages.json')) {
    $content = file_get_contents('messages.json');
    $messages = json_decode($content, true);
    if (!is_array($messages)) {
        $messages = [];
    }
}

array_unshift($messages, $data);
$messages = array_slice($messages, 0, 100);

file_put_contents('messages.json', json_encode($messages));
echo json_encode(['status' => 'ok']);
