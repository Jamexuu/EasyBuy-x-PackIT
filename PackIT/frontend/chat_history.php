<?php
// frontend/chat_history.php
// Returns chat history for the current session or logged-in user using your chat_history table schema.
//
// Response: { success: true, history: [ { prompt, response, time }, ... ] }

session_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$userId = $_SESSION['user']['id'] ?? null;
$sessionId = session_id();
$history = [];

// Try ChatBot class first (existing behavior)
if (file_exists(__DIR__ . '/../api/classes/chatBot.php')) {
    try {
        require_once __DIR__ . '/../api/classes/chatBot.php';
        if (class_exists('ChatBot')) {
            $bot = new ChatBot();
            $result = $bot->getHistory($userId, $sessionId);
            if (is_array($result) && !empty($result)) {
                foreach ($result as $item) {
                    if (isset($item['prompt']) && isset($item['response'])) {
                        $history[] = [
                            'prompt' => (string)$item['prompt'],
                            'response' => (string)$item['response'],
                            'time' => $item['created_at'] ?? ($item['time'] ?? null),
                        ];
                    } elseif (isset($item[0]) && isset($item[1])) {
                        $history[] = [
                            'prompt' => (string)$item[0],
                            'response' => (string)$item[1],
                            'time' => $item[2] ?? null,
                        ];
                    }
                }
                echo json_encode(['success' => true, 'history' => $history], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
    } catch (Throwable $e) {
        // ignore and fallback to DB
    }
}

// Fallback to DB using /api/db.php
$pdo = null;
$dbPath = __DIR__ . '/../api/db.php';
if (file_exists($dbPath)) {
    try {
        require_once $dbPath;
        if (!isset($pdo) || !($pdo instanceof PDO)) {
            if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
                $pdo = $GLOBALS['pdo'];
            }
        }
    } catch (Throwable $e) {
        $pdo = null;
    }
}

if ($pdo instanceof PDO) {
    try {
        // Query history for session_id OR user_id
        $sql = "SELECT prompt, response, created_at FROM chat_history WHERE session_id = :sid OR user_id = :uid ORDER BY created_at ASC LIMIT 500";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':sid' => $sessionId, ':uid' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {
            $history[] = [
                'prompt' => isset($r['prompt']) ? (string)$r['prompt'] : '',
                'response' => isset($r['response']) ? (string)$r['response'] : '',
                'time' => $r['created_at'] ?? null,
            ];
        }
    } catch (Throwable $e) {
        error_log('chat_history DB read failed: ' . $e->getMessage());
    }
}

// Return
echo json_encode(['success' => true, 'history' => $history], JSON_UNESCAPED_UNICODE);
exit;
?>