<?php
session_start();
require_once __DIR__ . '/../api/classes/Database.php';

if (!isset($_SESSION['driver_id'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$driverId = (int)$_SESSION['driver_id'];

/* CSRF token */
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

/* Helper: check whether driver currently has an active booking */
function driver_has_active_booking($db, $driverId) {
    $stmt = $db->executeQuery(
        "SELECT COUNT(*) AS c FROM bookings WHERE driver_id = ? AND tracking_status IN ('accepted','picked_up','in_transit')",
        [$driverId]
    );
    $row = $db->fetch($stmt);
    return !empty($row) && ((int)$row[0]['c'] > 0);
}

/* Handle logout (POST preferred) */
if ((isset($_GET['action']) && $_GET['action'] === 'logout') || (isset($_POST['action']) && $_POST['action'] === 'logout')) {
    session_destroy();
    header("Location: login.php");
    exit;
}

/* Handle vehicle management (add / set active / remove) */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = 'Invalid request (CSRF).';
        header('Location: driverProfile.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'add_vehicle') {
        $vehicleId = (int)($_POST['vehicle_id'] ?? 0);
        $licensePlate = trim((string)($_POST['license_plate'] ?? ''));
        if ($vehicleId <= 0) {
            $_SESSION['flash_error'] = 'Please select a vehicle to add.';
        } else {
            $stmt = $db->executeQuery("SELECT id FROM driver_vehicles WHERE driver_id = ? AND vehicle_id = ? LIMIT 1", [$driverId, $vehicleId]);
            $exists = $db->fetch($stmt);
            if (!empty($exists)) {
                $_SESSION['flash_error'] = 'You have already added that vehicle.';
            } else {
                $db->executeQuery("INSERT INTO driver_vehicles (driver_id, vehicle_id, license_plate) VALUES (?, ?, ?)", [$driverId, $vehicleId, $licensePlate ?: null]);
                $_SESSION['flash_success'] = 'Vehicle added to your profile.';
            }
        }
        header('Location: driverProfile.php');
        exit;
    }

    if ($action === 'set_active') {
        $dvId = (int)($_POST['dv_id'] ?? 0);
        // Check active booking first — do not allow switching while there's an active assignment
        if (driver_has_active_booking($db, $driverId)) {
            $_SESSION['flash_error'] = 'Cannot change active vehicle while you have an ongoing assignment. Complete it first.';
            header('Location: driverProfile.php');
            exit;
        }

        // Ensure the driver owns this dv record
        $stmt = $db->executeQuery("SELECT dv.vehicle_id, v.name FROM driver_vehicles dv JOIN vehicles v ON dv.vehicle_id = v.id WHERE dv.id = ? AND dv.driver_id = ? LIMIT 1", [$dvId, $driverId]);
        $row = $db->fetch($stmt);
        if (empty($row)) {
            $_SESSION['flash_error'] = 'Vehicle not found.';
        } else {
            $vehicleId = (int)$row[0]['vehicle_id'];
            $vehicleName = $row[0]['name'] ?? null;
            $db->executeQuery("UPDATE drivers SET active_vehicle_id = ?, vehicle_type = ? WHERE id = ?", [$vehicleId, $vehicleName, $driverId]);
            $_SESSION['flash_success'] = 'Active vehicle updated.';
        }
        header('Location: driverProfile.php');
        exit;
    }

    if ($action === 'remove_vehicle') {
        $dvId = (int)($_POST['dv_id'] ?? 0);
        // Fetch dv row
        $stmt = $db->executeQuery("SELECT vehicle_id FROM driver_vehicles WHERE id = ? AND driver_id = ? LIMIT 1", [$dvId, $driverId]);
        $row = $db->fetch($stmt);
        if (empty($row)) {
            $_SESSION['flash_error'] = 'Vehicle not found.';
            header('Location: driverProfile.php');
            exit;
        }

        $vehicleId = (int)$row[0]['vehicle_id'];

        // If driver currently has an active booking, prevent removing the vehicle if it's the active vehicle
        $stmt2 = $db->executeQuery("SELECT active_vehicle_id FROM drivers WHERE id = ? LIMIT 1", [$driverId]);
        $drv = $db->fetch($stmt2);
        $activeVid = !empty($drv) ? (int)($drv[0]['active_vehicle_id'] ?? 0) : 0;

        if ($activeVid === $vehicleId && driver_has_active_booking($db, $driverId)) {
            $_SESSION['flash_error'] = 'Cannot remove the active vehicle while you have an ongoing assignment. Complete it first or set a different active vehicle.';
            header('Location: driverProfile.php');
            exit;
        }

        // Proceed to delete
        $db->executeQuery("DELETE FROM driver_vehicles WHERE id = ? AND driver_id = ?", [$dvId, $driverId]);

        // If removed vehicle was active, unset active_vehicle_id on drivers
        if ($activeVid === $vehicleId) {
            $db->executeQuery("UPDATE drivers SET active_vehicle_id = NULL, vehicle_type = NULL WHERE id = ?", [$driverId]);
        }

        $_SESSION['flash_success'] = 'Vehicle removed.';
        header('Location: driverProfile.php');
        exit;
    }
}

/* Fetch driver basic info */
$stmt = $db->executeQuery("SELECT first_name, last_name, email, contact_number, vehicle_type, license_plate, active_vehicle_id FROM drivers WHERE id = ? LIMIT 1", [$driverId]);
$rows = $db->fetch($stmt);
if (empty($rows)) {
    session_destroy();
    header("Location: login.php");
    exit;
}
$driver = $rows[0];
$fullName = trim(($driver['first_name'] ?? '') . ' ' . ($driver['last_name'] ?? ''));

/* fetch all vehicles available in catalog for selection */
$stmt = $db->executeQuery("SELECT id, name FROM vehicles ORDER BY name ASC");
$vehiclesCatalog = $db->fetch($stmt);

/* fetch vehicles owned by this driver */
$stmt = $db->executeQuery("SELECT dv.id AS dv_id, dv.vehicle_id, v.name AS vehicle_name, dv.license_plate FROM driver_vehicles dv JOIN vehicles v ON dv.vehicle_id = v.id WHERE dv.driver_id = ? ORDER BY v.name", [$driverId]);
$ownedVehicles = $db->fetch($stmt);

$activeVehicleId = isset($driver['active_vehicle_id']) ? (int)$driver['active_vehicle_id'] : null;

/* Whether the driver has an active (in-progress) assignment */
$hasActiveAssignment = driver_has_active_booking($db, $driverId);
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
        .profile-card { background-color: var(--brand-yellow); border-radius: 24px; padding: 30px 20px; text-align: center; }
        .profile-img-container { position: relative; width: 120px; height: 120px; border-radius: 50%; overflow: hidden; border: 5px solid white; background: #eee; margin: 0 auto; }
        .menu-outline { border: 2px solid #eee; background: white; border-radius: 12px; padding: 18px; }
        .menu-link { display: flex; justify-content: space-between; align-items: center; padding: 12px 5px; color: #212529; text-decoration: none; font-weight: 600; border-bottom: 1px solid #f1f1f1; }
        .menu-link:last-child { border-bottom: none; }
        .vehicle-row { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:8px 0; border-bottom:1px solid #f3f3f3; }
        .vehicle-controls form { display:inline-block; margin-left:6px; }
    </style>
</head>
<body>
    <?php include("../frontend/components/driverNavbar.php"); ?>
    <div class="container my-5">
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="profile-card shadow-sm">
                    <div class="profile-img-container shadow-sm">
                        <img
                             src="https://ui-avatars.com/api/?name=<?php echo urlencode($fullName); ?>&background=random"
                             alt="Profile" style="width:100%; height:100%; object-fit:cover;">
                    </div>

                    <div class="mt-3">
                        <h3 class="fw-bold mb-0"><?php echo htmlspecialchars($fullName); ?></h3>
                        <p class="text-dark opacity-75 mb-3">Professional Driver</p>
                    </div>

                    <div class="bg-white rounded-4 p-3 mt-3 text-start shadow-sm">
                        <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Contact</div>
                        <div class="fw-bold mb-2"><?php echo htmlspecialchars($driver['contact_number'] ?? ''); ?></div>

                        <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Active Vehicle</div>
                        <div class="fw-bold"><?php
                            if ($activeVehicleId) {
                                $found = null;
                                foreach ($ownedVehicles as $ov) {
                                    if ((int)$ov['vehicle_id'] === $activeVehicleId) { $found = $ov['vehicle_name']; break; }
                                }
                                echo htmlspecialchars($found ?? '—');
                            } else {
                                echo '<span class="text-muted">Not set</span>';
                            }
                        ?></div>

                        <?php if ($hasActiveAssignment): ?>
                            <div class="mt-2 small text-warning">You have an ongoing assignment — you cannot change the active vehicle until it is completed.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="menu-outline shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Driver Vehicles & Settings</h5>
                        <a href="driver.php" class="btn btn-warning btn-sm">
                            <i class="bi bi-speedometer2 me-1"></i> Back to Dashboard
                        </a>
                    </div>

                    <!-- Add vehicle form -->
                    <form method="post" class="mb-3 d-flex gap-2 align-items-center">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" value="add_vehicle">
                        <select name="vehicle_id" class="form-select" required style="max-width:250px;">
                            <option value="">Select vehicle</option>
                            <?php foreach ($vehiclesCatalog as $v): ?>
                                <option value="<?php echo (int)$v['id']; ?>"><?php echo htmlspecialchars($v['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="license_plate" class="form-control" placeholder="License plate (optional)" style="max-width:220px;">
                        <button class="btn btn-warning">Add Vehicle</button>
                    </form>

                    <!-- Active vehicle switcher (dropbox) -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Switch Active Vehicle</label>
                        <?php if (empty($ownedVehicles)): ?>
                            <div class="small text-muted">No vehicles to choose from. Add a vehicle above.</div>
                        <?php else: ?>
                            <form method="post" class="d-flex gap-2 align-items-center">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="action" value="set_active">
                                <select name="dv_id" class="form-select" style="max-width:420px;">
                                    <?php foreach ($ownedVehicles as $ov): ?>
                                        <option value="<?php echo (int)$ov['dv_id']; ?>" <?php echo ((int)$ov['vehicle_id'] === $activeVehicleId) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($ov['vehicle_name'] . (!empty($ov['license_plate']) ? " — {$ov['license_plate']}" : '')); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-warning" <?php echo $hasActiveAssignment ? 'disabled title="Finish current assignment before changing active vehicle."' : ''; ?>>Set Active</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <!-- Owned vehicles list with remove controls -->
                    <div class="mb-3">
                        <h6 class="mb-2">Your Vehicles</h6>
                        <?php if (empty($ownedVehicles)): ?>
                            <div class="small text-muted">You haven't added any vehicles yet.</div>
                        <?php else: ?>
                            <?php foreach ($ownedVehicles as $v): ?>
                                <div class="vehicle-row">
                                    <div>
                                        <strong><?php echo htmlspecialchars($v['vehicle_name']); ?></strong>
                                        <?php if (!empty($v['license_plate'])): ?>
                                            <div class="small text-muted">Plate: <?php echo htmlspecialchars($v['license_plate']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="vehicle-controls">
                                        <?php if ((int)$v['vehicle_id'] === $activeVehicleId): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php endif; ?>

                                        <form method="post" onsubmit="return confirm('Remove this vehicle from your profile?');" style="display:inline-block;">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="action" value="remove_vehicle">
                                            <input type="hidden" name="dv_id" value="<?php echo (int)$v['dv_id']; ?>">
                                            <button class="btn btn-sm btn-outline-danger" <?php echo ($hasActiveAssignment && (int)$v['vehicle_id'] === $activeVehicleId) ? 'disabled title="Cannot remove active vehicle while you have an assignment."' : ''; ?>>Remove</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <div class="mt-3">
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="action" value="logout">
                            <button type="submit" class="menu-link mt-4 text-danger border-0" style="background:none;">
                                <span><i class="bi bi-box-arrow-right me-2"></i> Logout</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <?php include("../frontend/components/driverFooter.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>