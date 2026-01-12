<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

require_once __DIR__ . '/../api/classes/Database.php';

$db = new Database();
$pdo = $db->pdo();

if (!($pdo instanceof PDO)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database not available.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT
            id,
            subject,
            category,
            message,
            status,
            admin_reply,
            created_at,
            replied_at,
            acknowledged_at,
            user_unread
        FROM user_feedback
        WHERE user_id = :uid
        ORDER BY COALESCE(replied_at, acknowledged_at, created_at) DESC
        LIMIT 100
    ");
    $stmt->execute([':uid' => $userId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'items' => $rows], JSON_UNESCAPED_UNICODE);
    exit;
} catch (Throwable $e) {
    error_log("myFeedbackFetch error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
?>