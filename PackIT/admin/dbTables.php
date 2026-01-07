<?php
require_once '../api/classes/Auth.php';
require_once '../api/classes/Database.php';

Auth::requireAdmin();

$basePath = '../';

$viewToTable = [
    'users'     => 'users',
    'addresses' => 'addresses',
    'drivers'   => 'drivers',
    'vehicles'  => 'vehicles',
    'payments'  => 'payments',
    'bookings'  => 'bookings',
];

$view = $_GET['view'] ?? 'users';
$activePage = array_key_exists($view, $viewToTable) ? $view : 'users';

$db = new Database();
$conn = $db->connect();

// Strict whitelist
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

// Logic to disable "Next" button if we have fewer rows than the limit
$hasNextPage = count($rows) === $limit;

function prettyTitle(string $key): string {
    return match ($key) {
        'users' => 'Users',
        'addresses' => 'Addresses',
        'drivers' => 'Drivers',
        'vehicles' => 'Vehicles',
        'payments' => 'Payments',
        'bookings' => 'Bookings',
        default => ucfirst($key),
    };
}

// Helper to make data look nice (Badges for status, formatting dates)
function formatValue($col, $val) {
    if ($val === null) return '<span class="text-muted fst-italic">NULL</span>';
    
    // Format Dates
    if (str_contains($col, 'date') || str_contains($col, 'created_at') || str_contains($col, 'updated_at')) {
        $time = strtotime($val);
        if ($time) return '<span class="text-secondary small">' . date('M j, Y • g:i A', $time) . '</span>';
    }

    // Format IDs (Monospace)
    if (str_contains($col, 'id') || str_contains($col, 'code')) {
        return '<code class="text-dark bg-light px-1 rounded">' . htmlspecialchars($val) . '</code>';
    }

    // Format Status (Simple Badges)
    if (str_contains($col, 'status') || str_contains($col, 'role')) {
        $statusClass = match(strtolower($val)) {
            'active', 'completed', 'paid', 'delivered' => 'bg-success-subtle text-success',
            'pending', 'processing', 'transit' => 'bg-warning-subtle text-warning-emphasis',
            'cancelled', 'failed', 'inactive', 'rejected' => 'bg-danger-subtle text-danger',
            'driver', 'admin' => 'bg-primary-subtle text-primary',
            default => 'bg-secondary-subtle text-secondary'
        };
        return "<span class='badge $statusClass fw-medium px-2 py-1'>" . htmlspecialchars(ucfirst($val)) . "</span>";
    }

    // Truncate long text
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
            background-color: #f4f6f9; /* Light gray background */
        }
        .content-area {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02); /* Very subtle shadow */
        }
        h4.page-title {
            color: #111827;
            font-weight: 600;
            letter-spacing: -0.5px;
        }
        /* Table Styling */
        .table-custom {
            margin-bottom: 0;
        }
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
        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }
        .table-custom tbody tr:hover {
            background-color: #f9fafb;
        }
        /* Pagination Buttons */
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

        <div class="col-lg-9 col-md-8">
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($rows) === 0): ?>
                                    <tr>
                                        <td colspan="<?= max(1, count($columns)) ?>" class="text-center py-5">
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

    </div></div><?php include '../frontend/components/adminFooter.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>