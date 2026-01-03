<?php
require_once '../api/classes/Auth.php';
Auth::requireAdmin();
$user = Auth::getUser();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - PackIT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg" style="background: linear-gradient(90deg, rgba(57, 130, 80, 1) 8%, rgba(255, 196, 64, 1) 87%);">
  <div class="container-fluid">
    <a class="navbar-brand text-white fw-semibold" href="dashboard.php">PackIT Admin</a>
    <div class="d-flex gap-2">
        <a class="btn btn-light btn-sm" href="dbTables.php">DB Tables</a>
        <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">
    <h3 class="mb-3">Welcome, <?php echo htmlspecialchars($user['name'] ?? 'Admin'); ?></h3>
    <p class="text-muted mb-4">Use the menu to view PackIT database tables.</p>

    <div class="card p-4">
        <h5 class="mb-2">Quick links</h5>
        <ul class="mb-0">
            <li><a href="dbTables.php">View all PackIT tables</a></li>
        </ul>
    </div>
</div>
</body>
</html>