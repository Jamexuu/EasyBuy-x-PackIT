<?php
session_start();
require_once __DIR__ . '/../api/classes/Database.php';

if (!isset($_SESSION['driver_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$driverId = (int)$_SESSION['driver_id'];

$stmt = $db->executeQuery("SELECT first_name, is_available FROM drivers WHERE id = ? LIMIT 1", [$driverId]);
$rows = $db->fetch($stmt);

if (empty($rows)) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$driverName = $rows[0]['first_name'];
$isAvailable = (int)$rows[0]['is_available'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Driver Dashboard | PackIT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --brand-yellow: #fce354; --brand-dark: #111; }
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .status-toggle-card { background-color: var(--brand-dark); color: white; border-radius: 20px; padding: 15px 25px; }
        .action-card { background: white; border: none; border-radius: 20px; transition: transform 0.2s; text-decoration: none; color: var(--brand-dark); }
        .action-card:hover { transform: translateY(-5px); color: var(--brand-dark); }
        .icon-box { width: 50px; height: 50px; background-color: var(--brand-yellow); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .delivery-card { border-left: 8px solid var(--brand-yellow); border-radius: 20px; background: white; }
        .earnings-card { background: linear-gradient(135deg, var(--brand-yellow) 0%, #f7d938 100%); border-radius: 20px; border: none; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold mb-0">Hello, <?php echo htmlspecialchars($driverName); ?>!</h2>
            <p class="text-muted">Welcome to your driver dashboard.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="driver_profile.php" class="btn btn-dark rounded-pill px-4 me-2">
                <i class="bi bi-person-circle me-2"></i>Profile
            </a>
            <div class="status-toggle-card d-inline-flex align-items-center shadow-sm">
                <span class="me-3 fw-bold small">GO ONLINE</span>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="onlineSwitch"
                        <?php echo $isAvailable === 1 ? 'checked' : ''; ?>
                        style="width: 2.5em; height: 1.25em; cursor: pointer;">
                </div>
            </div>
        </div>
    </div>

    <!-- Keep the rest of your temporary UI as-is -->
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>