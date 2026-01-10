<?php
// frontend/notificationsMarkRead.php
// Marks feedback notifications as read for the logged-in user.
// Accepts POST: csrf_token and optional id (single feedback row id). If id omitted, marks all unread as read.
// Returns JSON { success: bool, message: string }

declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

// CSRF check
$csrf = (string)($_POST['csrf_token'] ?? '');
if (!isset($_SESSION['csrf_token']) || !hash_equals((string)$_SESSION['csrf_token'], $csrf)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid CSRF token'
    ]);
    exit;
}

// ✅ Correct include path (DO NOT include ../PackIT/...)
require_once __DIR__ . '/../api/classes/Database.php';

$database = new Database();
$pdo = $database->pdo();

if (!($pdo instanceof PDO)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database not available.']);
    exit;
}

$idRaw = $_POST['id'] ?? null;
$id = ($idRaw !== null && $idRaw !== '') ? (int)$idRaw : null;

try {
    if ($id !== null && $id > 0) {
        $stmt = $pdo->prepare("
            UPDATE user_feedback
            SET user_unread = 0
            WHERE id = :id AND user_id = :uid
        ");
        $stmt->execute([':id' => $id, ':uid' => $userId]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE user_feedback
            SET user_unread = 0
            WHERE user_id = :uid AND user_unread = 1
        ");
        $stmt->execute([':uid' => $userId]);
    }

    echo json_encode(['success' => true, 'message' => 'Marked as read']);
    exit;
} catch (Throwable $e) {
    error_log("notificationsMarkRead error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
?>