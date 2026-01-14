<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

session_start();

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
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

/**
 * Normalize PH numbers to E.164 (+63...) to match SmsLogs.RecipientNumber.
 * Examples:
 * - 09912345678 => +639912345678
 * - 9912345678  => +639912345678 (optional support)
 * - +6399...    => +6399...
 */
function toE164Ph(?string $raw): ?string {
    if ($raw === null) return null;
    $s = trim($raw);
    if ($s === '') return null;

    // keep leading +
    if (str_starts_with($s, '+')) {
        // normalize spaces/dashes
        return preg_replace('/[^\d\+]/', '', $s);
    }

    // remove non-digits
    $digits = preg_replace('/\D+/', '', $s);
    if ($digits === '') return null;

    // 09xxxxxxxxx => +639xxxxxxxxx
    if (str_starts_with($digits, '09') && strlen($digits) === 11) {
        return '+63' . substr($digits, 1);
    }

    // 9xxxxxxxxx (10 digits) => +639xxxxxxxxx (optional)
    if (str_starts_with($digits, '9') && strlen($digits) === 10) {
        return '+63' . $digits;
    }

    // 63xxxxxxxxxx => +63xxxxxxxxxx
    if (str_starts_with($digits, '63')) {
        return '+'.$digits;
    }

    // fallback: return digits as-is (might not match)
    return $digits;
}

try {
    // Fetch user's contact number so we can match smslogs.RecipientNumber
    $uStmt = $pdo->prepare("SELECT contact_number FROM users WHERE id = :uid LIMIT 1");
    $uStmt->execute([':uid' => $userId]);
    $userContact = (string)($uStmt->fetchColumn() ?: '');
    $userE164 = toE164Ph($userContact);

    // -----------------------------
    // 1) Feedback notifications (unread)
    // -----------------------------
    $fbStmt = $pdo->prepare("
        SELECT
            id,
            subject,
            message,
            admin_reply,
            status,
            COALESCE(replied_at, acknowledged_at, created_at) AS updated_at
        FROM user_feedback
        WHERE user_id = :uid AND user_unread = 1
        ORDER BY updated_at DESC
        LIMIT 20
    ");
    $fbStmt->execute([':uid' => $userId]);
    $fbRows = $fbStmt->fetchAll(PDO::FETCH_ASSOC);

    $items = [];

    foreach ($fbRows as $r) {
        $previewText = !empty($r['admin_reply']) ? $r['admin_reply'] : $r['message'];
        $excerpt = mb_substr(
            trim(preg_replace('/\s+/', ' ', strip_tags((string)$previewText))),
            0,
            200
        );

        $items[] = [
            'type'        => 'feedback',
            'id'          => (int)$r['id'],
            'subject'     => $r['subject'] ?? 'Feedback update',
            'excerpt'     => $excerpt,
            'status'      => $r['status'] ?? 'open',
            'time'        => date('M j, H:i', strtotime((string)$r['updated_at'])),
            'admin_reply' => $r['admin_reply'] ?? null,
        ];
    }

    $fbCntStmt = $pdo->prepare("SELECT COUNT(*) FROM user_feedback WHERE user_id = :uid AND user_unread = 1");
    $fbCntStmt->execute([':uid' => $userId]);
    $fbCount = (int)$fbCntStmt->fetchColumn();

    // -----------------------------
    // 2) SMS notifications (unread)
    // Requires smslogs.IsRead + ReadAt columns (you said you added them)
    // -----------------------------
    $smsCount = 0;

    if ($userE164) {
        // Fetch unread SMS logs for this user number
        $smsStmt = $pdo->prepare("
            SELECT
                Id,
                BookingId,
                DriverId,
                Status,
                Message,
                IsSent,
                ErrorMessage,
                CreatedAt
            FROM smslogs
            WHERE RecipientNumber = :num
              AND (IsRead = 0 OR IsRead IS NULL)
            ORDER BY CreatedAt DESC
            LIMIT 20
        ");
        $smsStmt->execute([':num' => $userE164]);
        $smsRows = $smsStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($smsRows as $s) {
            $msg = (string)($s['Message'] ?? '');
            $excerpt = mb_substr(
                trim(preg_replace('/\s+/', ' ', (string)$msg)),
                0,
                200
            );

            $bookingId = $s['BookingId'] ?? null;

            $items[] = [
                'type'        => 'sms',
                'id'          => (int)$s['Id'],
                'booking_id'  => ($bookingId !== null ? (int)$bookingId : null),
                'subject'     => 'Booking update' . ($bookingId ? (' #' . (int)$bookingId) : ''),
                'excerpt'     => $excerpt,
                'status'      => (string)($s['Status'] ?? ''),
                'time'        => date('M j, H:i', strtotime((string)$s['CreatedAt'])),
                'is_sent'     => (int)($s['IsSent'] ?? 0),
                'error'       => $s['ErrorMessage'] ?? null,
            ];
        }

        $smsCntStmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM smslogs
            WHERE RecipientNumber = :num
              AND (IsRead = 0 OR IsRead IS NULL)
        ");
        $smsCntStmt->execute([':num' => $userE164]);
        $smsCount = (int)$smsCntStmt->fetchColumn();
    }

    // -----------------------------
    // Merge + sort by time desc (best-effort)
    // -----------------------------
    usort($items, function($a, $b) {
        $ta = strtotime($a['time'] ?? '') ?: 0;
        $tb = strtotime($b['time'] ?? '') ?: 0;
        return $tb <=> $ta;
    });

    // Keep dropdown manageable
    $items = array_slice($items, 0, 20);

    $count = $fbCount + $smsCount;

    echo json_encode([
        'success' => true,
        'count'   => $count,
        'items'   => $items,
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Throwable $e) {
    error_log("notificationsFetch error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
?>