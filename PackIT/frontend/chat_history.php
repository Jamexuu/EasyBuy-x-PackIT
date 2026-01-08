<?php
// frontend/chat_history.php
// Returns chat history JSON for current session or logged-in user.
// GET only.

declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) session_start();

// include DB
require_once __DIR__ . '/../api/db.php';

$session_id = session_id();
$user_id = $_SESSION['user']['id'] ?? null;

try {
    if ($user_id !== null) {
        // fetch last 100 for user
        $stmt = $pdo->prepare("SELECT id, prompt, response, created_at FROM chat_history WHERE user_id = :uid ORDER BY created_at ASC LIMIT 100");
        $stmt->execute([':uid' => $user_id]);
    } else {
        // session-based history
        $stmt = $pdo->prepare("SELECT id, prompt, response, created_at FROM chat_history WHERE session_id = :sid ORDER BY created_at ASC LIMIT 100");
        $stmt->execute([':sid' => $session_id]);
    }

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $items = [];
    foreach ($rows as $r) {
        $items[] = [
            'id' => (int)$r['id'],
            'prompt' => $r['prompt'],
            'response' => $r['response'],
            'created_at' => $r['created_at']
        ];
    }

    echo json_encode(['success' => true, 'history' => $items]);
    exit;
} catch (Exception $e) {
    error_log('Chat history fetch error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Could not fetch history.']);
    exit;
}