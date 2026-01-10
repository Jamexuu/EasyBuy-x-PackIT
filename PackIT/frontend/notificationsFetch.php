<?php
// frontend/notificationsFetch.php
// Returns unread notifications (feedback updates) for the logged-in user in JSON.
// Response: { success: true, count: int, items: [ { id, subject, excerpt, status, time, admin_reply? } ] }

declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

session_start();

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

// Load Database class and get PDO
require_once __DIR__ . '/../api/classes/Database.php';

$database = new Database();
$pdo = $database->pdo();

if (!($pdo instanceof PDO)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database not available.']);
    exit;
}

try {
    // Fetch unread notifications for the user (user_unread = 1)
    $stmt = $pdo->prepare("
        SELECT
            id,
            subject,
            message,
            admin_reply,
            status,
            COALESCE(acknowledged_at, created_at) AS updated_at
        FROM user_feedback
        WHERE user_id = :uid AND user_unread = 1
        ORDER BY updated_at DESC
        LIMIT 20
    ");
    $stmt->execute([':uid' => $userId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $items = [];
    foreach ($rows as $r) {
        $previewText = !empty($r['admin_reply']) ? $r['admin_reply'] : $r['message'];
        $excerpt = mb_substr(
            trim(preg_replace('/\s+/', ' ', strip_tags((string)$previewText))),
            0,
            200
        );

        $items[] = [
            'id'          => (int)$r['id'],
            'subject'     => $r['subject'] ?? 'Feedback update',
            'excerpt'     => $excerpt,
            'status'      => $r['status'],
            'time'        => date('M j, H:i', strtotime((string)$r['updated_at'])),
            'admin_reply' => $r['admin_reply'] ?? null,
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