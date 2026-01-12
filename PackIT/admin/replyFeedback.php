<?php
require_once '../api/classes/Auth.php';
require_once '../api/classes/Database.php';

Auth::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dbTables.php?view=userFeedback");
    exit;
}

$feedbackId = isset($_POST['feedback_id']) ? (int)$_POST['feedback_id'] : 0;
$status     = isset($_POST['status']) ? trim($_POST['status']) : 'open';
$adminReply = isset($_POST['admin_reply']) ? trim($_POST['admin_reply']) : '';

if ($feedbackId <= 0) {
    header("Location: dbTables.php?view=userFeedback&error=invalid_id");
    exit;
}

// If you want to require a reply message, uncomment:
/*
if ($adminReply === '') {
    header("Location: dbTables.php?view=userFeedback&error=empty_reply");
    exit;
}
*/

$db = new Database();
$conn = $db->connect();

/**
 * ✅ Key fixes:
 * - user_unread = 1        -> user will see a notification
 * - replied_at = NOW()     -> track reply timestamp
 * - acknowledged_at = NOW()-> optional "last updated" timestamp (your table has it)
 *
 * NOTE: We are NOT touching handled_by to avoid FK issues.
 */
$sql = "UPDATE user_feedback
        SET admin_reply = ?,
            status = ?,
            user_unread = 1,
            replied_at = NOW(),
            acknowledged_at = NOW()
        WHERE id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    header("Location: dbTables.php?view=userFeedback&error=stmt_error");
    exit;
}

$stmt->bind_param("ssi", $adminReply, $status, $feedbackId);

if ($stmt->execute()) {
    header("Location: dbTables.php?view=userFeedback&msg=replied");
    exit;
}

header("Location: dbTables.php?view=userFeedback&error=db_error");
exit;
?>