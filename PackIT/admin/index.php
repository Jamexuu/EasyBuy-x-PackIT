<?php
// --- BACKEND LOGIC ---
require_once '../api/classes/Auth.php';
require_once '../api/classes/Database.php';

// Ensure user is logged in as Admin
Auth::requireAdmin();

$db = new Database();
$conn = $db->connect();

// Get the requested table from URL, default to empty (Dashboard view)
$selected = $_GET['table'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10; // Rows per page
$offset = ($page - 1) * $limit;

$rows = [];
$columns = [];
$error = null;
$total_rows = 0;
$total_pages = 0;

// If a table is selected in the URL, fetch its data
if ($selected !== '') {
    // Security: Whitelist allowed tables to prevent SQL Injection via URL
    // Make sure these match your actual Database Table names exactly
    $allowed_tables = ['users', 'address', 'drivers', 'vehicles', 'payments', 'bookings'];

    if (!in_array($selected, $allowed_tables, true)) {
        $error = "Invalid or unauthorized table selected.";
    } else {
        // 1. Get Columns
        $colRes = $conn->query("DESCRIBE `$selected`");
        if ($colRes) {
            while ($c = $colRes->fetch_assoc()) {
                $columns[] = $c['Field'];
            }
        }

        // 2. Count Total Rows (for pagination)
        $countRes = $conn->query("SELECT COUNT(*) as count FROM `$selected`");
        if ($countRes) {
            $row = $countRes->fetch_assoc();
            $total_rows = $row['count'];
            $total_pages = ceil($total_rows / $limit);
        }

        // 3. Get Data
        $dataRes = $conn->query("SELECT * FROM `$selected` LIMIT $limit OFFSET $offset");
        if ($dataRes) {
            while ($r = $dataRes->fetch_assoc()) {
                $rows[] = $r;
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PackIT Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --brand-yellow: #f8e15b;
            --brand-dark: #111;
            --border-gray: #dee2e6;
        }

        body {
            background-color: #f8f9fa;
        }

        .bg-brand {
            background-color: var(--brand-yellow) !important;
        }

        /* --- Admin Sidebar Styling --- */
        .admin-nav-card {
            display: block;
            width: 100%;
            padding: 1.5rem 1rem;
            text-align: center;
            text-decoration: none;
            color: var(--brand-dark);
            background-color: white;
            border: 2px solid var(--border-gray);
            border-radius: 1rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .admin-nav-card:hover {
            border-color: var(--brand-yellow);
            transform: translateY(-2px);
            color: var(--brand-dark);
            background-color: white;
        }

        /* Active State (Yellow Background) */
        .admin-nav-card.active {
            background-color: var(--brand-yellow);
            border-color: var(--brand-yellow);
            font-weight: bold;
        }

        .admin-nav-card.active:hover {
            background-color: var(--brand-yellow);
            border-color: var(--brand-yellow);
        }

        /* Main Content Area */
        .content-area {
            background-color: white;
            border-radius: 1rem;
            border: 1px solid var(--border-gray);
            min-height: 80vh;
        }

        /* Table Styling override */
        .table th {
            font-size: 0.85rem;
            text-transform: uppercase;
            color: #666;
        }

        .table td {
            font-size: 0.9rem;
            vertical-align: middle;
        }
    </style>
</head>

<body>

    <div class="container">
        <nav class="navbar navbar-expand-lg my-3 mx-auto rounded-pill shadow px-4 py-2 bg-brand" style="max-width: 95%;">
            <div class="container-fluid">

                <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
                    <img src="/EasyBuy-x-PackIT/PackIT/assets/LOGO.svg" alt="PackIT" height="40" class="object-fit-contain">
                    <span class="fw-bold">PackIT Admin</span>
                </a>

                <a href="logout.php" class="text-dark text-decoration-none fw-bold text-uppercase lh-1 d-none d-lg-block" style="font-size: 0.9rem;">
                    Logout <i class="bi bi-box-arrow-right ms-1"></i>
                </a>

                <button class="navbar-toggler border-0 p-0 ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="offcanvas offcanvas-end rounded-start-5" tabindex="-1" id="offcanvasNavbar">
                <div class="offcanvas-header bg-brand">
                    <h5 class="offcanvas-title fw-bold">MENU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body d-flex flex-column justify-content-center align-items-center">
                    <a href="logout.php" class="btn btn-dark w-100 rounded-pill text-uppercase fw-bold d-lg-none mt-auto mb-3">
                        Logout
                    </a>
                </div>
            </div>

    </div>
    </nav>
    </div>

    <div class="container pb-5">
        <div class="row g-4">

            <div class="col-lg-3 col-md-4">
                <div class="d-grid gap-3" id="sidebarMenu">
                    <a href="?table=users" class="admin-nav-card shadow-sm <?= $selected == 'users' ? 'active' : '' ?>">Users</a>
                    <a href="?table=address" class="admin-nav-card shadow-sm <?= $selected == 'address' ? 'active' : '' ?>">Address</a>
                    <a href="?table=drivers" class="admin-nav-card shadow-sm <?= $selected == 'drivers' ? 'active' : '' ?>">Drivers</a>
                    <a href="?table=vehicles" class="admin-nav-card shadow-sm <?= $selected == 'vehicles' ? 'active' : '' ?>">Vehicles</a>
                    <a href="?table=payments" class="admin-nav-card shadow-sm <?= $selected == 'payments' ? 'active' : '' ?>">Payments</a>
                    <a href="?table=bookings" class="admin-nav-card shadow-sm <?= $selected == 'bookings' ? 'active' : '' ?>">Bookings</a>
                </div>
            </div>

            <div class="col-lg-9 col-md-8">
                <div class="content-area shadow-sm p-4 p-lg-5">

                    <?php if ($selected === ''): ?>
                        <h4 class="fw-bold mb-4">Dashboard Overview</h4>
                        <div class="alert alert-light border text-center py-5">
                            <img src="/EasyBuy-x-PackIT/PackIT/assets/LOGO.svg" height="80" class="mb-3 opacity-50" style="filter: grayscale(100%);">
                            <p class="text-muted fw-bold">Select a module from the menu to view records.</p>
                        </div>

                    <?php elseif ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>

                    <?php else: ?>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold text-capitalize mb-0"><?= htmlspecialchars($selected) ?></h4>
                            <span class="badge bg-secondary rounded-pill">Total: <?= $total_rows ?></span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <?php foreach ($columns as $col): ?>
                                            <th><?= htmlspecialchars($col) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($rows) === 0): ?>
                                        <tr>
                                            <td colspan="<?= count($columns) ?>" class="text-center py-5 text-muted">
                                                No records found in this table.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($rows as $r): ?>
                                            <tr>
                                                <?php foreach ($columns as $col): ?>
                                                    <td>
                                                        <?= htmlspecialchars(mb_strimwidth((string)($r[$col] ?? ''), 0, 50, "...")) ?>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($total_pages > 1): ?>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <small class="text-muted">Page <?= $page ?> of <?= $total_pages ?></small>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link text-dark" href="?table=<?= urlencode($selected) ?>&page=<?= max(1, $page - 1) ?>">Previous</a>
                                        </li>
                                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                            <a class="page-link text-dark" href="?table=<?= urlencode($selected) ?>&page=<?= min($total_pages, $page + 1) ?>">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>