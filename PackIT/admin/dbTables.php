<?php
require_once '../api/classes/Auth.php';
require_once '../api/classes/Database.php';

Auth::requireAdmin();
$basePath = '../';
// ... [Keep PHP Logic identical to original up to HTML start] ...
$viewToTable = [
    'users' => 'users', 'addresses' => 'addresses', 'drivers' => 'drivers',
    'vehicles' => 'vehicles', 'payments' => 'payments', 'bookings' => 'bookings',
    'smslogs' => 'smslogs', 'driver_vehicles' => 'driver_vehicles',
    'password_resets' => 'password_resets', 'chat_history' => 'chat_history',
    'userFeedback' => 'user_feedback',
];
$view = $_GET['view'] ?? 'users';
$activePage = array_key_exists($view, $viewToTable) ? $view : 'users';
$db = new Database();
$conn = $db->connect();
$tables = [];
$res = $conn->query("SHOW TABLES");
if ($res) while ($row = $res->fetch_array()) $tables[] = $row[0];
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
    $colRes = $conn->query("DESCRIBE `$selectedTable`");
    if ($colRes) while ($c = $colRes->fetch_assoc()) $columns[] = $c['Field'];
    $dataRes = $conn->query("SELECT * FROM `$selectedTable` LIMIT $limit OFFSET $offset");
    if ($dataRes) while ($r = $dataRes->fetch_assoc()) $rows[] = $r;
}
$hasNextPage = count($rows) === $limit;

function prettyTitle(string $key): string {
    return ucfirst(str_replace('_', ' ', $key));
}

function formatValue($col, $val) {
    if ($val === null) return '<span class="text-muted fst-italic">NULL</span>';
    if (str_contains($col, 'date') || str_contains($col, 'created_at') || str_contains($col, 'updated_at')) {
        $time = strtotime($val);
        if ($time) return '<span class="text-secondary small">' . date('M j, Y • g:i A', $time) . '</span>';
    }
    if (str_contains($col, 'status') || str_contains($col, 'role')) {
        $color = match(strtolower($val)) {
            'active', 'completed', 'paid', 'success' => 'success',
            'pending', 'processing' => 'warning',
            'cancelled', 'failed', 'rejected' => 'danger',
            'driver', 'admin' => 'primary',
            default => 'secondary'
        };
        return "<span class='badge bg-{$color}-subtle text-{$color}-emphasis px-2 py-1'>" . htmlspecialchars(ucfirst($val)) . "</span>";
    }
    if (strlen($val) > 50) return '<span title="'.htmlspecialchars($val).'">' . htmlspecialchars(substr($val, 0, 47)) . '...</span>';
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
</head>
<body class="bg-light">

<?php include __DIR__ . '/../frontend/components/adminNavbar.php'; ?>

    <div class="col-lg-10 col-md-9"> 
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            
            <div class="card-header bg-white border-bottom p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h4 class="fw-bold mb-1"><?= htmlspecialchars($activePageTitle) ?></h4>
                        <div class="text-muted small">
                            Table: <code class="text-primary"><?= htmlspecialchars($selectedTable) ?></code>
                        </div>
                    </div>
                    <div class="badge bg-light text-dark border p-2 fw-normal">
                        Page <strong><?= $page ?></strong> • Showing <?= count($rows) ?> rows
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <?php if ($error): ?>
                    <div class="alert alert-danger m-4 rounded-3 shadow-sm">
                        <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <?php foreach ($columns as $col): ?>
                                        <th class="text-nowrap text-uppercase small text-muted px-4 py-3"><?= htmlspecialchars(str_replace('_', ' ', $col)) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rows)): ?>
                                    <tr>
                                        <td colspan="<?= count($columns) ?>" class="text-center py-5">
                                            <p class="text-muted fw-bold mb-0">No records found.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rows as $r): ?>
                                        <tr>
                                            <?php foreach ($columns as $col): ?>
                                                <td class="text-nowrap px-4 py-3">
                                                    <?= formatValue($col, $r[$col] ?? null) ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!$error): ?>
            <div class="card-footer bg-light border-top p-3 d-flex justify-content-between">
                <a class="btn btn-outline-secondary btn-sm <?= $page <= 1 ? 'disabled' : '' ?>"
                   href="<?= $page > 1 ? "?view=".urlencode($activePage)."&page=".($page - 1) : '#' ?>">
                   &larr; Previous
                </a>
                <a class="btn btn-outline-secondary btn-sm <?= !$hasNextPage ? 'disabled' : '' ?>"
                   href="<?= $hasNextPage ? "?view=".urlencode($activePage)."&page=".($page + 1) : '#' ?>">
                   Next &rarr;
                </a>
            </div>
            <?php endif; ?>

        </div>
    </div>

</div> </div> <?php include '../frontend/components/adminFooter.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>