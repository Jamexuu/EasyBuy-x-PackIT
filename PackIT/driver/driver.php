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

/* --- Handle POST actions --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    /* AJAX: toggle availability */
    if ($action === 'toggle_availability') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            header('Content-Type: application/json', true, 400);
            echo json_encode(['success' => false, 'message' => 'Invalid request (CSRF).']);
            exit;
        }

        $value = (isset($_POST['value']) && ((string)$_POST['value'] === '1')) ? 1 : 0;

        // FIX: update only once
        $db->executeQuery("UPDATE drivers SET is_available = ? WHERE id = ?", [$value, $driverId]);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'is_available' => $value]);
        exit;
    }

    /* Non-AJAX actions */
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = 'Invalid request';
        header('Location: driver.php');
        exit;
    }

    $bookingId = (int)($_POST['booking_id'] ?? 0);

    /* ACCEPT booking */
    if ($action === 'accept' && $bookingId > 0) {
        // Get active vehicle name/type (NO DB CHANGES)
        $stmt = $db->executeQuery(
            "SELECT d.vehicle_type, v.name AS active_vehicle_name
             FROM drivers d
             LEFT JOIN vehicles v ON d.active_vehicle_id = v.id
             WHERE d.id = ?
             LIMIT 1",
            [$driverId]
        );
        $driverRow = $db->fetch($stmt);

        $activeVehicleName = null;
        if (!empty($driverRow)) {
            if (!empty($driverRow[0]['vehicle_type'])) {
                $activeVehicleName = $driverRow[0]['vehicle_type'];
            } elseif (!empty($driverRow[0]['active_vehicle_name'])) {
                $activeVehicleName = $driverRow[0]['active_vehicle_name'];
            }
        }

        if (!$activeVehicleName) {
            $_SESSION['flash_error'] = 'Please set/select an active vehicle in your profile before accepting bookings.';
            header('Location: driver.php');
            exit;
        }

        // Ensure driver has no other active booking
        $stmt = $db->executeQuery(
            "SELECT COUNT(*) AS c
             FROM bookings
             WHERE driver_id = ? AND tracking_status IN ('accepted','picked_up','in_transit')",
            [$driverId]
        );
        $cntRow = $db->fetch($stmt);
        $existingCount = !empty($cntRow) ? (int)$cntRow[0]['c'] : 0;

        if ($existingCount > 0) {
            $_SESSION['flash_error'] = 'You already have an active booking. Finish it before accepting another.';
            header('Location: driver.php');
            exit;
        }

        // Claim booking (must match vehicle_type)
        $stmt = $db->executeQuery(
            "UPDATE bookings
             SET driver_id = ?, tracking_status = 'accepted', updated_at = CURRENT_TIMESTAMP()
             WHERE id = ?
               AND tracking_status = 'pending'
               AND (driver_id IS NULL OR driver_id = 0)
               AND vehicle_type = ?",
            [$driverId, $bookingId, $activeVehicleName]
        );

        $affected = mysqli_stmt_affected_rows($stmt);
        if ($affected > 0) {
            $_SESSION['flash_success'] = 'Booking accepted.';
            header('Location: driverBookings.php');
            exit;
        } else {
            $_SESSION['flash_error'] = 'Booking was already taken, no longer pending, or does not match your active vehicle.';
        }
    }

    header('Location: driver.php');
    exit;
}

/* Fetch driver info (NO $driverName USED) */
$stmt = $db->executeQuery(
    "SELECT d.is_available, d.active_vehicle_id, d.vehicle_type, v.name AS active_vehicle_name
     FROM drivers d
     LEFT JOIN vehicles v ON d.active_vehicle_id = v.id
     WHERE d.id = ?
     LIMIT 1",
    [$driverId]
);
$rows = $db->fetch($stmt);

if (empty($rows)) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$isAvailable = (int)($rows[0]['is_available'] ?? 0);
$activeVehicleId = isset($rows[0]['active_vehicle_id']) ? (int)$rows[0]['active_vehicle_id'] : null;

// Use vehicle_type if present; else fallback to vehicles.name; else empty string
$activeVehicleName = '';
if (!empty($rows[0]['vehicle_type'])) {
    $activeVehicleName = (string)$rows[0]['vehicle_type'];
} elseif (!empty($rows[0]['active_vehicle_name'])) {
    $activeVehicleName = (string)$rows[0]['active_vehicle_name'];
}

/* Vehicles owned (optional display) */
$stmt = $db->executeQuery(
    "SELECT dv.id AS dv_id, dv.vehicle_id, v.name AS vehicle_name, dv.license_plate
     FROM driver_vehicles dv
     JOIN vehicles v ON dv.vehicle_id = v.id
     WHERE dv.driver_id = ?
     ORDER BY v.name ASC",
    [$driverId]
);
$ownedVehicles = $db->fetch($stmt);

/* Active booking check (disable accept) */
$stmt = $db->executeQuery(
    "SELECT COUNT(*) AS c
     FROM bookings
     WHERE driver_id = ? AND tracking_status IN ('accepted','picked_up','in_transit')",
    [$driverId]
);
$cRow = $db->fetch($stmt);
$hasActiveAssignment = (!empty($cRow) && isset($cRow[0]['c'])) ? ((int)$cRow[0]['c'] > 0) : false;

/* Pending bookings only */
$pendingBookings = [];
if ($isAvailable === 1 && $activeVehicleName !== '') {
    $stmtPending = $db->executeQuery(
        "SELECT b.*, u.first_name AS user_first_name, u.last_name AS user_last_name
         FROM bookings b
         JOIN users u ON b.user_id = u.id
         WHERE b.tracking_status = 'pending'
           AND b.vehicle_type = ?
         ORDER BY b.created_at ASC",
        [$activeVehicleName]
    );
    $pendingBookings = $db->fetch($stmtPending);
}
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
        .booking-card { border-radius: 12px; padding: 1rem; margin-bottom: 1rem; background: white; }
        .small-muted { font-size: .9rem; color: #6c757d; }
        .list-container { max-height: 65vh; overflow-y: auto; padding-right: 8px; }
        @media (max-width: 767px) { .list-container { max-height: none; } }
        .vehicle-pill { display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:999px; background:white; border:1px solid #e9ecef; margin-right:8px; }
        .vehicle-active { border-color: var(--brand-yellow); box-shadow:0 2px 8px rgba(0,0,0,0.04); }
    </style>
</head>
<body>

<?php include __DIR__ . "/../frontend/components/driverNavbar.php"; ?>

<div class="container py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold mb-0">Pending Bookings</h2>
            <p class="text-muted">Home shows pending bookings only.</p>
            <div class="mt-2">
                <strong>Active vehicle:</strong>
                <?php if ($activeVehicleName !== ''): ?>
                    <span class="vehicle-pill vehicle-active"><?php echo htmlspecialchars($activeVehicleName); ?></span>
                <?php else: ?>
                    <span class="text-muted small">No active vehicle — set one in your profile to see bookings.</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6 text-md-end">
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

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['flash_success']); ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error']); ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-md-7">
            <h5>Pending List</h5>
            <div class="list-container">
                <?php if ($isAvailable !== 1): ?>
                    <div class="alert alert-warning">You are currently offline. Toggle <strong>GO ONLINE</strong> to see pending bookings.</div>
                <?php else: ?>
                    <?php if ($activeVehicleName === ''): ?>
                        <div class="alert alert-info">You have no active vehicle selected. Go to Profile to set one.</div>
                    <?php elseif ($hasActiveAssignment): ?>
                        <div class="alert alert-secondary">
                            You already have an active booking. You can’t accept a new one until you finish it.
                            Go to <a href="driverBookings.php">Bookings</a>.
                        </div>
                    <?php elseif (empty($pendingBookings)): ?>
                        <div class="alert alert-info">No pending bookings for your active vehicle at the moment.</div>
                    <?php else: ?>
                        <?php foreach ($pendingBookings as $b): ?>
                            <div class="booking-card shadow-sm">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1">Booking #<?php echo htmlspecialchars($b['id']); ?> — <small class="small-muted">Pending</small></h6>
                                        <div class="small-muted mb-2">
                                            Customer: <?php echo htmlspecialchars($b['user_first_name'] . ' ' . $b['user_last_name']); ?>
                                            — Amount: ₱<?php echo number_format($b['total_amount'], 2); ?>
                                        </div>
                                        <div><strong>Pickup:</strong> <span class="small-muted"><?php echo htmlspecialchars(implode(', ', array_filter([$b['pickup_house'], $b['pickup_barangay'], $b['pickup_municipality'], $b['pickup_province']]))); ?></span></div>
                                        <div><strong>Drop:</strong> <span class="small-muted"><?php echo htmlspecialchars(implode(', ', array_filter([$b['drop_house'], $b['drop_barangay'], $b['drop_municipality'], $b['drop_province']]))); ?></span></div>
                                    </div>
                                    <div class="text-end align-self-center">
                                        <form method="post" style="display:inline-block;">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="booking_id" value="<?php echo (int)$b['id']; ?>">
                                            <input type="hidden" name="action" value="accept">
                                            <button type="submit" class="btn btn-primary">Accept</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-5">
            <h5>Your Vehicles</h5>
            <?php if (empty($ownedVehicles)): ?>
                <div class="alert alert-light">No vehicles registered. Add vehicles in your <a href="driverProfile.php">profile</a>.</div>
            <?php else: ?>
                <?php foreach ($ownedVehicles as $v): ?>
                    <div class="vehicle-pill <?php echo ((int)$v['vehicle_id'] === $activeVehicleId) ? 'vehicle-active' : ''; ?>">
                        <strong><?php echo htmlspecialchars($v['vehicle_name']); ?></strong>
                        <?php if (!empty($v['license_plate'])): ?>
                            <small class="text-muted"> — <?php echo htmlspecialchars($v['license_plate']); ?></small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="small text-muted mt-2">To add vehicles or change active vehicle, go to <a href="driverProfile.php">Profile</a>.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../frontend/components/driverFooter.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const onlineSwitch = document.getElementById('onlineSwitch');
    if (!onlineSwitch) return;

    onlineSwitch.addEventListener('change', async function () {
        const checked = this.checked ? 1 : 0;
        this.disabled = true;

        const params = new URLSearchParams();
        params.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
        params.append('action', 'toggle_availability');
        params.append('value', checked);

        try {
            const res = await fetch('driver.php', {
                method: 'POST',
                body: params,
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            const json = await res.json();
            if (json && json.success) {
                location.reload();
            } else {
                alert('Failed to change online status: ' + (json?.message ?? 'Unknown error'));
                this.checked = !this.checked;
            }
        } catch (err) {
            alert('Network error. Please try again.');
            this.checked = !this.checked;
        } finally {
            this.disabled = false;
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>