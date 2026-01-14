<?php
// frontend/notificationsMarkRead.php
// Marks notifications as read for the logged-in user.
// Supports:
// - feedback: user_feedback.user_unread = 0
// - sms: smslogs.IsRead = 1, ReadAt = NOW() for user's RecipientNumber
//
// Accepts POST:
// - csrf_token (required)
// - type: feedback|sms (optional, default: feedback)
// - id: optional (single row id). If omitted, marks all unread of that type.
//
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

require_once __DIR__ . '/../api/classes/Database.php';

$database = new Database();
$pdo = $database->pdo();

if (!($pdo instanceof PDO)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database not available.']);
    exit;
}

function toE164Ph(?string $raw): ?string {
    if ($raw === null) return null;
    $s = trim($raw);
    if ($s === '') return null;

    if (str_starts_with($s, '+')) {
        return preg_replace('/[^\d\+]/', '', $s);
    }

    $digits = preg_replace('/\D+/', '', $s);
    if ($digits === '') return null;

    if (str_starts_with($digits, '09') && strlen($digits) === 11) {
        return '+63' . substr($digits, 1);
    }
    if (str_starts_with($digits, '9') && strlen($digits) === 10) {
        return '+63' . $digits;
    }
    if (str_starts_with($digits, '63')) {
        return '+'.$digits;
    }
    return $digits;
}

$type = strtolower(trim((string)($_POST['type'] ?? 'feedback')));
$idRaw = $_POST['id'] ?? null;
$id = ($idRaw !== null && $idRaw !== '') ? (int)$idRaw : null;

try {
    if ($type === 'sms') {
        // Get user's e164 number
        $uStmt = $pdo->prepare("SELECT contact_number FROM users WHERE id = :uid LIMIT 1");
        $uStmt->execute([':uid' => $userId]);
        $userContact = (string)($uStmt->fetchColumn() ?: '');
        $userE164 = toE164Ph($userContact);

        if (!$userE164) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User contact number not available']);
            exit;
        }

        if ($id !== null && $id > 0) {
            // Mark a single sms row (ensure it belongs to the user via RecipientNumber)
            $stmt = $pdo->prepare("
                UPDATE smslogs
                SET IsRead = 1, ReadAt = NOW()
                WHERE Id = :id AND RecipientNumber = :num
            ");
            $stmt->execute([':id' => $id, ':num' => $userE164]);
        } else {
            // Mark all unread for user
            $stmt = $pdo->prepare("
                UPDATE smslogs
                SET IsRead = 1, ReadAt = NOW()
                WHERE RecipientNumber = :num AND (IsRead = 0 OR IsRead IS NULL)
            ");
            $stmt->execute([':num' => $userE164]);
        }

        echo json_encode(['success' => true, 'message' => 'SMS marked as read']);
        exit;
    }

    // Default: feedback
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

    echo json_encode(['success' => true, 'message' => 'Feedback marked as read']);
    exit;

} catch (Throwable $e) {
    error_log("notificationsMarkRead error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
?>