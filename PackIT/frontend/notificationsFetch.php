<?php
// frontend/notificationsFetch.php
// Returns unread notifications (feedback updates) for the logged-in user in JSON.
// Response: { success: true, count: int, items: [ { id, subject, excerpt, status, time } ] }

declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

session_start();

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

// Load DB adapter (expects $pdo from api/db.php)
$pdo = null;
$dbPath = __DIR__ . '/../api/db.php';
if (file_exists($dbPath)) {
    require_once $dbPath;
}

if (!isset($pdo) || !($pdo instanceof PDO)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database not available.']);
    exit;
}

try {
    // Fetch unread notifications for the user (user_unread = 1)
    $stmt = $pdo->prepare("
        SELECT id, subject, message, status, COALESCE(acknowledged_at, created_at) AS updated_at
        FROM user_feedback
        WHERE user_id = :uid AND user_unread = 1
        ORDER BY updated_at DESC
        LIMIT 20
    ");
    $stmt->execute([':uid' => $userId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $items = [];
    foreach ($rows as $r) {
        $excerpt = mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags($r['message'])),), 0, 200);
        $items[] = [
            'id' => (int)$r['id'],
            'subject' => $r['subject'] ?? 'Feedback update',
            'excerpt' => $excerpt,
            'status' => $r['status'],
            'time' => date('M j, H:i', strtotime($r['updated_at'])),
        ];
    }

    // Count unread
    $cntStmt = $pdo->prepare("SELECT COUNT(*) FROM user_feedback WHERE user_id = :uid AND user_unread = 1");
    $cntStmt->execute([':uid' => $userId]);
    $count = (int)$cntStmt->fetchColumn();

    echo json_encode(['success' => true, 'count' => $count, 'items' => $items], JSON_UNESCAPED_UNICODE);
    exit;
} catch (Throwable $e) {
    error_log("notificationsFetch error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
?>