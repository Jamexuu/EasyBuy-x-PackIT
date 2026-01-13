<?php
session_start();
require_once __DIR__ . '/../api/classes/Database.php';

if (!isset($_SESSION['driver_id'])) {
    header("Location: index.php");
    exit;
}

$db = new Database();
$driverId = (int)$_SESSION['driver_id'];

/* CSRF token */
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

/* Helper: check whether driver currently has an active booking */
function driver_has_active_booking($db, $driverId) {
    $stmt = $db->executeQuery(
        "SELECT COUNT(*) AS c
         FROM bookings
         WHERE driver_id = ?
           AND tracking_status IN ('accepted','picked_up','in_transit')",
        [$driverId]
    );
    $row = $db->fetch($stmt);
    return !empty($row) && ((int)$row[0]['c'] > 0);
}

/* Handle logout */
if ((isset($_GET['action']) && $_GET['action'] === 'logout') || (isset($_POST['action']) && $_POST['action'] === 'logout')) {
    session_destroy();
    header("Location: index.php");
    exit;
}

/* Handle vehicle management */
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
            $stmt = $db->executeQuery(
                "SELECT id FROM driver_vehicles WHERE driver_id = ? AND vehicle_id = ? LIMIT 1",
                [$driverId, $vehicleId]
            );
            $exists = $db->fetch($stmt);

            if (!empty($exists)) {
                $_SESSION['flash_error'] = 'You have already added that vehicle.';
            } else {
                $db->executeQuery(
                    "INSERT INTO driver_vehicles (driver_id, vehicle_id, license_plate)
                     VALUES (?, ?, ?)",
                    [$driverId, $vehicleId, ($licensePlate !== '' ? $licensePlate : null)]
                );
                $_SESSION['flash_success'] = 'Vehicle added to your profile.';
            }
        }
        header('Location: driverProfile.php');
        exit;
    }

    if ($action === 'set_active') {
        $dvId = (int)($_POST['dv_id'] ?? 0);

        if (driver_has_active_booking($db, $driverId)) {
            $_SESSION['flash_error'] = 'Cannot change active vehicle while you have an ongoing assignment. Complete it first.';
            header('Location: driverProfile.php');
            exit;
        }

        $stmt = $db->executeQuery(
            "SELECT dv.vehicle_id, v.name
             FROM driver_vehicles dv
             JOIN vehicles v ON dv.vehicle_id = v.id
             WHERE dv.id = ? AND dv.driver_id = ?
             LIMIT 1",
            [$dvId, $driverId]
        );
        $row = $db->fetch($stmt);

        if (empty($row)) {
            $_SESSION['flash_error'] = 'Vehicle not found.';
        } else {
            $vehicleId = (int)$row[0]['vehicle_id'];
            $vehicleName = $row[0]['name'] ?? null;

            $db->executeQuery(
                "UPDATE drivers SET active_vehicle_id = ?, vehicle_type = ? WHERE id = ?",
                [$vehicleId, $vehicleName, $driverId]
            );
            $_SESSION['flash_success'] = 'Current vehicle updated.';
        }
        header('Location: driverProfile.php');
        exit;
    }

    if ($action === 'remove_vehicle') {
        $dvId = (int)($_POST['dv_id'] ?? 0);

        $stmt = $db->executeQuery(
            "SELECT vehicle_id FROM driver_vehicles WHERE id = ? AND driver_id = ? LIMIT 1",
            [$dvId, $driverId]
        );
        $row = $db->fetch($stmt);

        if (empty($row)) {
            $_SESSION['flash_error'] = 'Vehicle not found.';
            header('Location: driverProfile.php');
            exit;
        }

        $vehicleId = (int)$row[0]['vehicle_id'];

        $stmt2 = $db->executeQuery("SELECT active_vehicle_id FROM drivers WHERE id = ? LIMIT 1", [$driverId]);
        $drv = $db->fetch($stmt2);
        $activeVid = !empty($drv) ? (int)($drv[0]['active_vehicle_id'] ?? 0) : 0;

        if ($activeVid === $vehicleId && driver_has_active_booking($db, $driverId)) {
            $_SESSION['flash_error'] = 'Cannot remove the active vehicle while you have an ongoing assignment. Complete it first or set a different active vehicle.';
            header('Location: driverProfile.php');
            exit;
        }

        $db->executeQuery("DELETE FROM driver_vehicles WHERE id = ? AND driver_id = ?", [$dvId, $driverId]);

        if ($activeVid === $vehicleId) {
            $db->executeQuery("UPDATE drivers SET active_vehicle_id = NULL, vehicle_type = NULL WHERE id = ?", [$driverId]);
        }

        $_SESSION['flash_success'] = 'Vehicle removed.';
        header('Location: driverProfile.php');
        exit;
    }
}

/* Fetch driver basic info */
$stmt = $db->executeQuery(
    "SELECT first_name, last_name, email, contact_number, vehicle_type, license_plate, active_vehicle_id
     FROM drivers
     WHERE id = ?
     LIMIT 1",
    [$driverId]
);
$rows = $db->fetch($stmt);
if (empty($rows)) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$driver = $rows[0];
$fullName = trim(($driver['first_name'] ?? '') . ' ' . ($driver['last_name'] ?? ''));
$email = $driver['email'] ?? '';
$contact = $driver['contact_number'] ?? '';

$activeVehicleId = isset($driver['active_vehicle_id']) ? (int)$driver['active_vehicle_id'] : 0;

/* Catalog */
$stmt = $db->executeQuery("SELECT id, name FROM vehicles ORDER BY name ASC");
$vehiclesCatalog = $db->fetch($stmt);

/* Owned vehicles */
$stmt = $db->executeQuery(
    "SELECT dv.id AS dv_id, dv.vehicle_id, v.name AS vehicle_name, dv.license_plate
     FROM driver_vehicles dv
     JOIN vehicles v ON dv.vehicle_id = v.id
     WHERE dv.driver_id = ?
     ORDER BY v.name",
    [$driverId]
);
$ownedVehicles = $db->fetch($stmt);

$hasActiveAssignment = driver_has_active_booking($db, $driverId);

/* current vehicle name */
$currentVehicleName = null;
if ($activeVehicleId > 0 && !empty($ownedVehicles)) {
    foreach ($ownedVehicles as $ov) {
        if ((int)$ov['vehicle_id'] === $activeVehicleId) {
            $currentVehicleName = $ov['vehicle_name'] ?? null;
            break;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Driver Profile | PackIT</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root { --brand-yellow: #fce354; --brand-dark: #212529; }

        body {
            background:#fff;
            min-height:100vh;
            display:flex;
            flex-direction:column;
        }

        .profile-card {
            background:var(--brand-yellow);
            border-radius:40px;
            padding:50px 20px;
            text-align:center;
        }

        .profile-img-container {
            width:180px;
            height:180px;
            border-radius:50%;
            overflow:hidden;
            border:5px solid #fff;
            background:#eee;
            margin:auto;
        }

        #profileDisplay { width:100%; height:100%; object-fit:cover; }

        .menu-outline {
            border:3px solid var(--brand-yellow);
            border-radius:35px;
            padding:30px;
        }

        .menu-link {
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:12px 0;
            text-decoration:none;
            font-size:1.1rem;
            font-weight:600;
            color:#212529;
        }

        .menu-link .icon { transition:.2s; }
        .menu-link[aria-expanded="true"] .icon { transform:rotate(180deg); }

        .vehicle-row {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            padding:10px 0;
            border-bottom:1px solid #f1f1f1;
        }
        .vehicle-row:last-child { border-bottom:none; }

        /* Custom Logout Button Style */
        .btn-brand-logout {
            background-color: var(--brand-yellow);
            color: var(--brand-dark);
            font-weight: 700;
            border: none;
            padding: 12px;
            border-radius: 15px;
            transition: all 0.2s ease;
        }
        .btn-brand-logout:hover {
            background-color: #f0d52d; /* Slightly darker shade for hover */
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>
<?php include("../frontend/components/driverNavbar.php"); ?>

<div class="container my-5">
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?php echo h($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?php echo h($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>

    <div class="row g-5 align-items-center">

        <div class="col-lg-4">
            <div class="profile-card shadow-sm">
                <div class="profile-img-container shadow-sm">
                    <img id="profileDisplay"
                         src="https://ui-avatars.com/api/?name=<?php echo urlencode($fullName); ?>&background=random"
                         alt="Profile">
                </div>

                <h2 class="fw-bold mt-3"><?php echo h($fullName); ?></h2>
                <p class="text-muted mb-1"><?php echo h($email); ?></p>
                <h5 class="mb-1"><?php echo h($contact ?: '--'); ?></h5>

                <div class="mt-3 bg-white rounded-4 p-3 text-start shadow-sm">
                    <div class="small text-muted text-uppercase fw-bold" style="font-size:.7rem;">Current Vehicle</div>
                    <div class="fw-bold">
                        <?php echo $activeVehicleId > 0 ? h($currentVehicleName ?: '—') : '<span class="text-muted">Not set</span>'; ?>
                    </div>

                    <?php if ($hasActiveAssignment): ?>
                        <div class="mt-2 small text-warning">
                            You have an ongoing assignment — vehicle changes are disabled until you finish it.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="menu-outline">

                <a class="menu-link" data-bs-toggle="collapse" href="#vehiclesSection" role="button" aria-expanded="false">
                    Vehicles <span class="icon">▾</span>
                </a>
                <div class="collapse" id="vehiclesSection">
                    <div class="pt-2">

                        <div class="mb-4">
                            <div class="small fw-bold mb-2">Change current vehicle</div>

                            <?php if (empty($ownedVehicles)): ?>
                                <div class="small text-muted">No vehicles found. Add one below.</div>
                            <?php else: ?>
                                <form method="post" class="d-flex gap-2 align-items-center flex-wrap">
                                    <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="action" value="set_active">

                                    <select name="dv_id" class="form-select" style="max-width:420px;">
                                        <?php foreach ($ownedVehicles as $ov): ?>
                                            <option value="<?php echo (int)$ov['dv_id']; ?>" <?php echo ((int)$ov['vehicle_id'] === $activeVehicleId) ? 'selected' : ''; ?>>
                                                <?php echo h($ov['vehicle_name'] . (!empty($ov['license_plate']) ? " — {$ov['license_plate']}" : '')); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <button class="btn btn-warning"
                                        <?php echo $hasActiveAssignment ? 'disabled title="Finish current assignment before changing current vehicle."' : ''; ?>>
                                        Set Current
                                    </button>
                                </form>
                            <?php endif; ?>

                            <div class="small text-muted mt-2">
                                Your current vehicle controls what bookings you can accept in the dashboard.
                            </div>
                        </div>

                        <hr>

                        <div class="mt-2">
                            <div class="small fw-bold mb-2">Your Vehicles</div>

                            <?php if (empty($ownedVehicles)): ?>
                                <div class="small text-muted">You haven't added any vehicles yet.</div>
                            <?php else: ?>
                                <?php foreach ($ownedVehicles as $v): ?>
                                    <div class="vehicle-row">
                                        <div>
                                            <div class="fw-semibold">
                                                <?php echo h($v['vehicle_name']); ?>
                                                <?php if ((int)$v['vehicle_id'] === $activeVehicleId): ?>
                                                    <span class="badge bg-success ms-2">Current</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($v['license_plate'])): ?>
                                                <div class="small text-muted">Plate: <?php echo h($v['license_plate']); ?></div>
                                            <?php endif; ?>
                                        </div>

                                        <form method="post"
                                              onsubmit="return confirm('Remove this vehicle from your profile?');"
                                              class="m-0">
                                            <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">
                                            <input type="hidden" name="action" value="remove_vehicle">
                                            <input type="hidden" name="dv_id" value="<?php echo (int)$v['dv_id']; ?>">
                                            <button class="btn btn-sm btn-outline-danger"
                                                <?php echo ($hasActiveAssignment && (int)$v['vehicle_id'] === $activeVehicleId)
                                                    ? 'disabled title="Cannot remove current vehicle while you have an assignment."'
                                                    : ''; ?>>
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>

                <a class="menu-link mt-3" data-bs-toggle="collapse" href="#registerVehicleSection" role="button" aria-expanded="false">
                    Register another vehicle <span class="icon">▾</span>
                </a>
                <div class="collapse" id="registerVehicleSection">
                    <div class="pt-2">
                        <form method="post" class="d-flex gap-2 align-items-center flex-wrap mb-0">
                            <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">
                            <input type="hidden" name="action" value="add_vehicle">

                            <select name="vehicle_id" class="form-select" required style="max-width:250px;">
                                <option value="">Select vehicle</option>
                                <?php foreach ($vehiclesCatalog as $v): ?>
                                    <option value="<?php echo (int)$v['id']; ?>"><?php echo h($v['name']); ?></option>
                                <?php endforeach; ?>
                            </select>

                            <input type="text" name="license_plate" class="form-control"
                                   placeholder="License plate (optional)" style="max-width:220px;">

                            <button class="btn btn-warning">Add Vehicle</button>
                        </form>

                        <div class="small text-muted mt-2">
                            Add another vehicle to your profile. You can set it as current under “Vehicles”.
                        </div>
                    </div>
                </div>

                <a class="menu-link mt-3" data-bs-toggle="collapse" href="#account" role="button" aria-expanded="false">
                    Account & Security <span class="icon">▾</span>
                </a>
                <div class="collapse" id="account">
                    <div class="pt-2">
                       <p class="small text-muted mb-0">
                           Manage your password and security settings via the main settings panel (Coming Soon).
                       </p>
                    </div>
                </div>

                <form method="post" class="mt-5">
                    <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="btn btn-brand-logout w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>

            </div>
        </div>

    </div>
</div>

<?php include("../frontend/components/driverFooter.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>