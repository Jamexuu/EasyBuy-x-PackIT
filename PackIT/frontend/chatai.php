<?php
// frontend/chatai.php
session_start();
require_once __DIR__ . '/../api/classes/chatBot.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$prompt = $_POST['prompt'] ?? '';
$userId = $_SESSION['user']['id'] ?? null;
$sessionId = session_id();

try {
    $bot = new ChatBot();
    $result = $bot->processMessage($userId, $sessionId, $prompt);
    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Internal Server Error']);
}
?>