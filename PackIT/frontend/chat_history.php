<?php
// frontend/chat_history.php
session_start();
require_once __DIR__ . '/../api/classes/chatBot.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$userId = $_SESSION['user']['id'] ?? null;
$sessionId = session_id();

try {
    $bot = new ChatBot();
    $history = $bot->getHistory($userId, $sessionId);
    echo json_encode(['success' => true, 'history' => $history]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Could not fetch history']);
}
?>