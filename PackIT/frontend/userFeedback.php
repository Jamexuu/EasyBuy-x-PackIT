<?php
declare(strict_types=1);

require_once __DIR__ . '/../api/classes/Auth.php';
Auth::requireAdmin();
$admin = Auth::getUser();

require_once __DIR__ . '/../api/classes/Database.php';

$activePage = 'userFeedback';
$basePath = '../';

$db = new Database();
$pdo = $db->pdo();

if (!($pdo instanceof PDO)) {
    http_response_code(500);
    die('Database not available.');
}

// Auth already starts the session, but guard anyway
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle reply submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reply') {
    $csrf = (string)($_POST['csrf_token'] ?? '');
    if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
        http_response_code(400);
        die('Invalid CSRF token');
    }

    $id = (int)($_POST['id'] ?? 0);
    $reply = trim((string)($_POST['admin_reply'] ?? ''));

    if ($id <= 0 || $reply === '') {
        header('Location: userFeedback.php?err=1');
        exit;
    }

    try {
        // Matches your actual table columns shown in phpMyAdmin:
        // - admin_reply
        // - replied_at
        // - handled_by
        // - user_unread
        // - status
        $stmt = $pdo->prepare("
            UPDATE user_feedback
            SET
                admin_reply = :reply,
                replied_at = NOW(),
                handled_by = :handled_by,
                status = 'replied',
                user_unread = 1
            WHERE id = :id
        ");
        $stmt->execute([
            ':reply' => $reply,
            ':handled_by' => $admin['id'] ?? null,
            ':id' => $id,
        ]);

        header('Location: userFeedback.php?ok=1');
        exit;
    } catch (Throwable $e) {
        error_log("Admin reply failed: " . $e->getMessage());
        header('Location: userFeedback.php?err=1');
        exit;
    }
}

// Filters
$statusFilter = trim((string)($_GET['status'] ?? ''));
$q = trim((string)($_GET['q'] ?? ''));

$where = "1=1";
$params = [];

if ($statusFilter !== '') {
    $where .= " AND status = :status";
    $params[':status'] = $statusFilter;
}

if ($q !== '') {
    // No JOIN, so we only search columns in user_feedback
    $where .= " AND (subject LIKE :q OR message LIKE :q OR category LIKE :q)";
    $params[':q'] = '%' . $q . '%';
}

// Fetch feedback list (NO JOIN to users table — avoids column mismatch like u.name/u.email)
$rows = [];
try {
    $stmt = $pdo->prepare("
        SELECT
            id,
            user_id,
            subject,
            category,
            message,
            status,
            admin_reply,
            user_unread,
            created_at,
            replied_at,
            handled_by
        FROM user_feedback
        WHERE {$where}
        ORDER BY created_at DESC
        LIMIT 200
    ");
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log("Fetch feedback list failed: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Feedback - PackIT Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<?php include __DIR__ . '/components/adminNavbar.php'; ?>

<div class="container pb-5">
    <div class="row g-4">
        <div class="col-12">
            <div class="content-area shadow-sm p-4 p-md-5">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">User Feedback</h4>
                        <div class="text-muted">Replying sets <code>user_unread=1</code> so the user gets notified.</div>
                    </div>

                    <form class="d-flex flex-wrap gap-2" method="get" action="userFeedback.php">
                        <input class="form-control" style="min-width: 240px;" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search subject/message/category">
                        <select class="form-select" name="status" style="min-width: 180px;">
                            <option value="" <?= $statusFilter === '' ? 'selected' : '' ?>>All statuses</option>
                            <option value="open" <?= $statusFilter === 'open' ? 'selected' : '' ?>>open</option>
                            <option value="replied" <?= $statusFilter === 'replied' ? 'selected' : '' ?>>replied</option>
                            <option value="closed" <?= $statusFilter === 'closed' ? 'selected' : '' ?>>closed</option>
                        </select>
                        <button class="btn btn-dark" type="submit">Filter</button>
                    </form>
                </div>

                <?php if (isset($_GET['ok'])): ?>
                    <div class="alert alert-success">Reply saved and user notified.</div>
                <?php elseif (isset($_GET['err'])): ?>
                    <div class="alert alert-danger">Could not save reply.</div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:90px;">ID</th>
                                <th style="width:130px;">User</th>
                                <th>Subject / Message</th>
                                <th style="width:140px;">Status</th>
                                <th style="width:170px;">Created</th>
                                <th style="width:140px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!$rows): ?>
                            <tr><td colspan="6" class="text-muted">No feedback found.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($rows as $r): ?>
                            <tr>
                                <td class="fw-semibold">#<?= (int)$r['id'] ?></td>

                                <td>
                                    <div class="fw-semibold">ID: <?= (int)$r['user_id'] ?></div>
                                </td>

                                <td style="max-width:520px;">
                                    <div class="fw-semibold"><?= htmlspecialchars($r['subject'] ?? '') ?></div>
                                    <div class="text-muted small">
                                        <?= htmlspecialchars(mb_substr(trim((string)$r['message']), 0, 200)) ?><?= mb_strlen((string)$r['message']) > 200 ? '…' : '' ?>
                                    </div>

                                    <?php if (!empty($r['admin_reply'])): ?>
                                        <div class="mt-2 p-2 border rounded bg-light">
                                            <div class="text-muted small">Admin reply:</div>
                                            <div style="white-space: pre-wrap;"><?= htmlspecialchars((string)$r['admin_reply']) ?></div>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="badge bg-secondary"><?= htmlspecialchars((string)($r['status'] ?? '')) ?></span>
                                    <?php if ((int)($r['user_unread'] ?? 0) === 1): ?>
                                        <span class="badge bg-warning text-dark">user_unread</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-muted small"><?= htmlspecialchars((string)$r['created_at']) ?></td>

                                <td>
                                    <button
                                        class="btn btn-sm btn-outline-dark"
                                        data-bs-toggle="modal"
                                        data-bs-target="#replyModal"
                                        data-id="<?= (int)$r['id'] ?>"
                                        data-subject="<?= htmlspecialchars($r['subject'] ?? '', ENT_QUOTES) ?>"
                                        data-message="<?= htmlspecialchars($r['message'] ?? '', ENT_QUOTES) ?>"
                                        data-reply="<?= htmlspecialchars($r['admin_reply'] ?? '', ENT_QUOTES) ?>"
                                    >
                                        <i class="bi bi-reply"></i> Reply
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form class="modal-content" method="post" action="userFeedback.php">
            <input type="hidden" name="action" value="reply">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="id" id="reply_id" value="">

            <div class="modal-header">
                <h5 class="modal-title">Reply to Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-2">
                    <div class="text-muted small">Subject</div>
                    <div class="fw-semibold" id="reply_subject"></div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small">User message</div>
                    <div class="border rounded p-3 bg-light" style="white-space: pre-wrap;" id="reply_message"></div>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-semibold">Admin reply</label>
                    <textarea class="form-control" name="admin_reply" id="reply_text" rows="6" required></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-dark" type="submit">Save reply</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/components/adminFooter.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const replyModal = document.getElementById('replyModal');
replyModal.addEventListener('show.bs.modal', event => {
    const btn = event.relatedTarget;

    document.getElementById('reply_id').value = btn.getAttribute('data-id') || '';
    document.getElementById('reply_subject').textContent = btn.getAttribute('data-subject') || '';
    document.getElementById('reply_message').textContent = btn.getAttribute('data-message') || '';
    document.getElementById('reply_text').value = btn.getAttribute('data-reply') || '';
});
</script>
</body>
</html>