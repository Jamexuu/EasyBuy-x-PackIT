<?php
require_once '../api/classes/Auth.php';
require_once '../api/classes/Database.php';

Auth::requireAdmin();

$db = new Database();
$conn = $db->connect();

// 1) Get list of tables
$tables = [];
$res = $conn->query("SHOW TABLES");
if ($res) {
    while ($row = $res->fetch_array()) {
        $tables[] = $row[0];
    }
}

$selected = $_GET['table'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 25;
$offset = ($page - 1) * $limit;

$rows = [];
$columns = [];
$error = null;

// whitelist table selection
if ($selected !== '') {
    if (!in_array($selected, $tables, true)) {
        $error = "Invalid table selected.";
    } else {
        // get columns
        $colRes = $conn->query("DESCRIBE `$selected`");
        if ($colRes) {
            while ($c = $colRes->fetch_assoc()) {
                $columns[] = $c['Field'];
            }
        }

        // get rows with pagination
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
    <title>PackIT - DB Tables</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg" style="background: linear-gradient(90deg, rgba(57, 130, 80, 1) 8%, rgba(255, 196, 64, 1) 87%);">
        <div class="container-fluid">
            <a class="navbar-brand text-white fw-semibold" href="dashboard.php">PackIT Admin</a>
            <div class="d-flex gap-2">
                <a class="btn btn-light btn-sm" href="dashboard.php">Dashboard</a>
                <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <h3 class="mb-3">Database Tables (PackIT)</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="GET" class="row g-2 align-items-end mb-3">
            <div class="col-12 col-md-6">
                <label class="form-label">Select table</label>
                <select name="table" class="form-select" onchange="this.form.submit()">
                    <option value="">-- choose --</option>
                    <?php foreach ($tables as $t): ?>
                        <option value="<?php echo htmlspecialchars($t); ?>" <?php echo ($t === $selected) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($t); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Page</label>
                <input type="number" name="page" class="form-control" value="<?php echo $page; ?>" min="1">
            </div>
            <div class="col-12 col-md-3">
                <button class="btn btn-success w-100" type="submit">View</button>
            </div>
        </form>

        <?php if ($selected !== '' && !$error): ?>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div><strong><?php echo htmlspecialchars($selected); ?></strong></div>
                    <div class="text-muted">Showing <?php echo count($rows); ?> rows (<?php echo $limit; ?> per page)</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-sm mb-0">
                        <thead>
                            <tr>
                                <?php foreach ($columns as $col): ?>
                                    <th><?php echo htmlspecialchars($col); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($rows) === 0): ?>
                                <tr>
                                    <td colspan="<?php echo max(1, count($columns)); ?>" class="text-center py-4 text-muted">No rows.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($rows as $r): ?>
                                    <tr>
                                        <?php foreach ($columns as $col): ?>
                                            <td><?php echo htmlspecialchars((string)($r[$col] ?? '')); ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a class="btn btn-outline-secondary btn-sm"
                        href="?table=<?php echo urlencode($selected); ?>&page=<?php echo max(1, $page - 1); ?>">Prev</a>
                    <a class="btn btn-outline-secondary btn-sm"
                        href="?table=<?php echo urlencode($selected); ?>&page=<?php echo $page + 1; ?>">Next</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>