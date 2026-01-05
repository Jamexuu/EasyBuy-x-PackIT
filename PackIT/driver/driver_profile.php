<?php
session_start();
require_once __DIR__ . '/../api/classes/Database.php';

if (!isset($_SESSION['driver_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$driverId = (int)$_SESSION['driver_id'];

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: login.php");
    exit;
}

$stmt = $db->executeQuery(
    "SELECT first_name, last_name, email, contact_number, vehicle_type, license_plate
     FROM drivers WHERE id = ? LIMIT 1",
    [$driverId]
);
$rows = $db->fetch($stmt);

if (empty($rows)) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$driver = $rows[0];
$fullName = $driver['first_name'] . ' ' . $driver['last_name'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Driver Profile | PackIT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --brand-yellow: #fce354; --brand-dark: #111; }
        body { background-color: #f8f9fa; min-height: 100vh; }
        .profile-card { background-color: var(--brand-yellow); border-radius: 40px; padding: 40px 20px; text-align: center; }
        .profile-img-container { position: relative; width: 150px; height: 150px; border-radius: 50%; overflow: hidden; border: 5px solid white; background: #eee; margin: 0 auto; }
        #profileDisplay { width: 100%; height: 100%; object-fit: cover; }
        .camera-icon-button { position: absolute; bottom: 0px; right: 10px; background: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid var(--brand-yellow); z-index: 10; }
        .menu-outline { border: 2px solid #eee; background: white; border-radius: 30px; padding: 25px; }
        .menu-link { display: flex; justify-content: space-between; align-items: center; padding: 15px 5px; color: #212529; text-decoration: none; font-weight: 600; border-bottom: 1px solid #f1f1f1; }
        .menu-link:last-child { border-bottom: none; }
        .badge-verified { background: #198754; color: white; font-size: 0.7rem; padding: 4px 8px; border-radius: 10px; vertical-align: middle; }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="profile-card shadow-sm">
                    <div class="profile-img-container shadow-sm">
                        <img id="profileDisplay"
                             src="https://ui-avatars.com/api/?name=<?php echo urlencode($fullName); ?>&background=random"
                             alt="Profile">
                    </div>

                    <div class="mt-3">
                        <h3 class="fw-bold mb-0"><?php echo htmlspecialchars($fullName); ?> <span class="badge-verified">VERIFIED</span></h3>
                        <p class="text-dark opacity-75 mb-3">Professional Driver</p>
                    </div>

                    <div class="bg-white rounded-4 p-3 mt-3 text-start shadow-sm">
                        <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">License Plate</div>
                        <div class="fw-bold mb-2"><?php echo htmlspecialchars($driver['license_plate']); ?></div>

                        <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Type of Vehicle</div>
                        <div class="fw-bold"><?php echo htmlspecialchars($driver['vehicle_type']); ?></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="menu-outline shadow-sm">
                    <h5 class="fw-bold mb-4">Driver Settings</h5>
                    <a href="driver_profile.php?action=logout" class="menu-link mt-4 text-danger border-0">
                        <span><i class="bi bi-box-arrow-right me-2"></i> Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>