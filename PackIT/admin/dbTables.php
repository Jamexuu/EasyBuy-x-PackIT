<?php
require_once '../api/classes/Auth.php';
require_once '../api/classes/Database.php';

Auth::requireAdmin();

$basePath = '../';

// View to Table Mapping
$viewToTable = [
    'users'           => 'users',
    'addresses'       => 'addresses',
    'drivers'         => 'drivers',
    'vehicles'        => 'vehicles',
    'payments'        => 'payments',
    'bookings'        => 'bookings',
    'smslogs'         => 'smslogs',
    'driver_vehicles' => 'driver_vehicles',
    'password_resets' => 'password_resets',
    'chat_history'    => 'chat_history', 
    'userFeedback'    => 'user_feedback', // Maps view 'userFeedback' to table 'user_feedback'
];

$view = $_GET['view'] ?? 'users';
// Ensure activePage logic matches the keys in $viewToTable
$activePage = array_key_exists($view, $viewToTable) ? $view : 'users';

$db = new Database();
$conn = $db->connect();

// Strict whitelist for security
$tables = [];
$res = $conn->query("SHOW TABLES");
if ($res) {
    while ($row = $res->fetch_array()) {
        $tables[] = $row[0];
    }
}

$selectedTable = $viewToTable[$activePage];
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 25;
$offset = ($page - 1) * $limit;

$rows = [];
$columns = [];
$error = null;

if (!in_array($selectedTable, $tables, true)) {
    $error = "Table not found: " . $selectedTable;
} else {
    // Get Columns
    $colRes = $conn->query("DESCRIBE `$selectedTable`");
    if ($colRes) {
        while ($c = $colRes->fetch_assoc()) {
            
            // --- UPDATED LOGIC START ---
            // List of columns to HIDE from the table view
            $hiddenColumns = [
                'replied_at', 
                'user_unread', 
                'acknowledged_at'
            ];

            // If the current column is in the hidden list, skip it
            if (in_array($c['Field'], $hiddenColumns)) {
                continue;
            }
            // --- UPDATED LOGIC END ---

            $columns[] = $c['Field'];
        }
    }

    // Get Data
    $dataRes = $conn->query("SELECT * FROM `$selectedTable` LIMIT $limit OFFSET $offset");
    if ($dataRes) {
        while ($r = $dataRes->fetch_assoc()) {
            $rows[] = $r;
        }
    }
}

$hasNextPage = count($rows) === $limit;

function prettyTitle(string $key): string {
    return match ($key) {
        'users'           => 'Users',
        'addresses'       => 'Addresses',
        'drivers'         => 'Drivers',
        'vehicles'        => 'Vehicles',
        'payments'        => 'Payments',
        'bookings'        => 'Bookings',
        'smslogs'         => 'SMS Logs',
        'driver_vehicles' => 'Driver Vehicles',
        'password_resets' => 'Password Resets',
        'chat_history'    => 'Chat History',
        'userFeedback'    => 'User Feedback',
        default           => ucfirst(str_replace('_', ' ', $key)),
    };
}

// Helper to make data look nice
function formatValue($col, $val) {
    if ($val === null) return '<span class="text-muted fst-italic">NULL</span>';
    
    if (str_contains($col, 'date') || str_contains($col, 'created_at') || str_contains($col, 'updated_at') || str_contains($col, 'expires_at')) {
        $time = strtotime($val);
        if ($time) return '<span class="text-secondary small">' . date('M j, Y • g:i A', $time) . '</span>';
    }

    if (str_contains($col, 'id') || str_contains($col, 'code') || str_contains($col, 'token')) {
        return '<code class="text-dark bg-light px-1 rounded">' . htmlspecialchars($val) . '</code>';
    }

    if (str_contains($col, 'status') || str_contains($col, 'role')) {
        $statusClass = match(strtolower($val)) {
            'active', 'completed', 'paid', 'delivered', 'sent', 'success', 'resolved' => 'bg-success-subtle text-success',
            'pending', 'processing', 'transit', 'queued', 'open' => 'bg-warning-subtle text-warning-emphasis',
            'cancelled', 'failed', 'inactive', 'rejected', 'error', 'closed' => 'bg-danger-subtle text-danger',
            'driver', 'admin' => 'bg-primary-subtle text-primary',
            default => 'bg-secondary-subtle text-secondary'
        };
        return "<span class='badge $statusClass fw-medium px-2 py-1'>" . htmlspecialchars(ucfirst($val)) . "</span>";
    }

    if (strlen($val) > 50) {
        return '<span title="'.htmlspecialchars($val).'">' . htmlspecialchars(substr($val, 0, 47)) . '...</span>';
    }

    return htmlspecialchars($val);
}

$activePageTitle = prettyTitle($activePage);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PackIT Admin - <?= htmlspecialchars($activePageTitle) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
        }
        .content-area {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }
        h4.page-title {
            color: #111827;
            font-weight: 600;
            letter-spacing: -0.5px;
        }
        /* Table Styling */
        .table-custom { margin-bottom: 0; }
        .table-custom thead th {
            background-color: #f9fafb;
            color: #6b7280;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            border-top: none;
        }
        .table-custom tbody td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            color: #374151;
            font-size: 0.875rem;
            border-bottom: 1px solid #f3f4f6;
        }
        .table-custom tbody tr:last-child td { border-bottom: none; }
        .table-custom tbody tr:hover { background-color: #f9fafb; }
        
        .btn-page {
            border: 1px solid #e5e7eb;
            color: #374151;
            background: white;
            padding: 0.4rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-page:hover:not(.disabled) {
            background-color: #f9fafb;
            border-color: #d1d5db;
            color: #111827;
        }
        .btn-page.disabled {
            background-color: #f3f4f6;
            color: #9ca3af;
            border-color: #e5e7eb;
            cursor: not-allowed;
            opacity: 0.7;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../frontend/components/adminNavbar.php'; ?>

        <div class="col-lg-10 col-md-9"> 
            <div class="content-area p-0 overflow-hidden">
                
                <div class="p-4 border-bottom border-light">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h4 class="page-title mb-1"><?= htmlspecialchars($activePageTitle) ?></h4>
                            <div class="text-secondary small">
                                Database Table: <code class="text-primary"><?= htmlspecialchars($selectedTable) ?></code>
                            </div>
                        </div>

                        <div class="text-secondary small d-flex align-items-center gap-2 bg-light px-3 py-2 rounded-pill border">
                            <span>Page <strong><?= $page ?></strong></span>
                            <span class="text-muted">|</span>
                            <span>Showing <?= count($rows) ?> rows</span>
                        </div>
                    </div>
                </div>

                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'replied'): ?>
                    <div class="p-4 pb-0">
                        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> Reply sent successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="p-4">
                        <div class="alert alert-danger mb-0 border-0 shadow-sm">
                            <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    </div>
                <?php else: ?>

                    <div class="table-responsive">
                        <table class="table table-custom align-middle">
                            <thead>
                                <tr>
                                    <?php foreach ($columns as $col): ?>
                                        <th class="text-nowrap"><?= htmlspecialchars(str_replace('_', ' ', $col)) ?></th>
                                    <?php endforeach; ?>
                                    
                                    <?php if ($activePage === 'userFeedback'): ?>
                                        <th class="text-nowrap text-end pe-4">Action</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($rows) === 0): ?>
                                    <tr>
                                        <td colspan="<?= count($columns) + ($activePage === 'userFeedback' ? 1 : 0) ?>" class="text-center py-5">
                                            <div class="text-muted mb-2" style="font-size: 2rem;">📂</div>
                                            <p class="text-muted fw-medium mb-0">No records found.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rows as $r): ?>
                                        <tr>
                                            <?php foreach ($columns as $col): ?>
                                                <td class="text-nowrap">
                                                    <?= formatValue($col, $r[$col] ?? null) ?>
                                                </td>
                                            <?php endforeach; ?>

                                            <?php if ($activePage === 'userFeedback'): ?>
                                                <td class="text-end pe-4">
                                                    <button class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-medium"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#replyModal"
                                                            data-id="<?= $r['id'] ?>"
                                                            data-user="<?= htmlspecialchars($r['user_id'] ?? 'Guest') ?>"
                                                            data-status="<?= htmlspecialchars($r['status'] ?? 'open') ?>"
                                                            data-message="<?= htmlspecialchars($r['message'] ?? '') ?>"
                                                            data-reply="<?= htmlspecialchars($r['admin_reply'] ?? '') ?>">
                                                        <i class="bi bi-pencil-square me-1"></i> Reply
                                                    </button>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-4 border-top border-light bg-light bg-opacity-10">
                        <a class="btn btn-page rounded-3 <?= $page <= 1 ? 'disabled' : '' ?>"
                           href="<?= $page > 1 ? "?view=".urlencode($activePage)."&page=".($page - 1) : '#' ?>">
                           &larr; Previous
                        </a>
                        
                        <a class="btn btn-page rounded-3 <?= !$hasNextPage ? 'disabled' : '' ?>"
                           href="<?= $hasNextPage ? "?view=".urlencode($activePage)."&page=".($page + 1) : '#' ?>">
                           Next &rarr;
                        </a>
                    </div>

                <?php endif; ?>

            </div>
        </div>

    </div> </div> <?php if ($activePage === 'userFeedback'): ?>
<div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Reply to Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="replyFeedback.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="feedback_id" id="modalFeedbackId">
                    
                    <div class="mb-3">
                        <label class="form-label text-secondary small text-uppercase fw-bold">User's Message</label>
                        <div class="p-3 bg-light rounded text-muted small fst-italic" id="modalUserMessage">
                            </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label fw-medium">Status</label>
                        <select class="form-select" name="status" id="modalStatus">
                            <option value="open">Open</option>
                            <option value="pending">Pending</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="admin_reply" class="form-label fw-medium">Admin Reply</label>
                        <textarea class="form-control" name="admin_reply" id="modalReply" rows="4" placeholder="Type your response here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Send Reply</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const replyModal = document.getElementById('replyModal');
        if (replyModal) {
            replyModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                
                // Extract info from data-* attributes
                const id = button.getAttribute('data-id');
                const status = button.getAttribute('data-status');
                const message = button.getAttribute('data-message');
                const reply = button.getAttribute('data-reply');

                // Update the modal's content
                replyModal.querySelector('#modalFeedbackId').value = id;
                replyModal.querySelector('#modalUserMessage').textContent = message ? '"' + message + '"' : '(No message)';
                replyModal.querySelector('#modalStatus').value = status;
                replyModal.querySelector('#modalReply').value = reply;
            });
        }
    });
</script>
<?php endif; ?>

<?php include '../frontend/components/adminFooter.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>