<?php
require_once '../api/classes/Auth.php';
require_once '../api/classes/Database.php';

Auth::requireAdmin();

$basePath = '../';

// ✅ Match your real PackIT DB table names (from image)
$viewToTable = [
    'users'     => 'users',
    'addresses' => 'addresses',
    'drivers'   => 'drivers',
    'payments'  => 'payments',
    'bookings'  => 'bookings',
];

$view = $_GET['view'] ?? 'users';
$activePage = array_key_exists($view, $viewToTable) ? $view : 'users';

$db = new Database();
$conn = $db->connect();

// Strict whitelist from DB
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
    $error = "Table not found in database: " . $selectedTable;
} else {
    $colRes = $conn->query("DESCRIBE `$selectedTable`");
    if ($colRes) {
        while ($c = $colRes->fetch_assoc()) {
            $columns[] = $c['Field'];
        }
    }

    $dataRes = $conn->query("SELECT * FROM `$selectedTable` LIMIT $limit OFFSET $offset");
    if ($dataRes) {
        while ($r = $dataRes->fetch_assoc()) {
            $rows[] = $r;
        }
    }
}

function prettyTitle(string $key): string {
    return match ($key) {
        'users' => 'Users',
        'addresses' => 'Addresses',
        'drivers' => 'Drivers',
        'payments' => 'Payments',
        'bookings' => 'Bookings',
        default => ucfirst($key),
    };
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
<body>

<?php
// Must be included after Auth::requireAdmin() so redirects still work
include __DIR__ . '/../frontend/components/adminNavbar.php';
?>

        <div class="col-lg-9 col-md-8">
            <div class="content-area shadow-sm p-5">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h4 class="fw-bold mb-1"><?= htmlspecialchars($activePageTitle) ?></h4>
                        <div class="text-muted">Table: <strong><?= htmlspecialchars($selectedTable) ?></strong></div>
                    </div>

                    <div class="text-muted small">
                        Page <strong><?= $page ?></strong> • <?= $limit ?> per page • Showing <strong><?= count($rows) ?></strong> rows
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm mb-0 align-middle">
                            <thead>
                                <tr>
                                    <?php foreach ($columns as $col): ?>
                                        <th class="text-nowrap"><?= htmlspecialchars($col) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($rows) === 0): ?>
                                    <tr>
                                        <td colspan="<?= max(1, count($columns)) ?>" class="text-center py-4 text-muted">
                                            No rows.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rows as $r): ?>
                                        <tr>
                                            <?php foreach ($columns as $col): ?>
                                                <td class="text-nowrap"><?= htmlspecialchars((string)($r[$col] ?? '')) ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <a class="btn btn-outline-dark btn-sm rounded-pill"
                           href="?view=<?= urlencode($activePage) ?>&page=<?= max(1, $page - 1) ?>">
                            Prev
                        </a>
                        <a class="btn btn-outline-dark btn-sm rounded-pill"
                           href="?view=<?= urlencode($activePage) ?>&page=<?= $page + 1 ?>">
                            Next
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div><!-- row -->
</div><!-- container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>