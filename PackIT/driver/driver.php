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

/* Helper: determine next status button label */
function next_button_label($status)
{
    return match ($status) {
        'accepted' => 'Mark Picked Up',
        'picked_up' => 'Mark In Transit',
        'in_transit' => 'Mark Delivered',
        default => null,
    };
}

/* Handle POST actions */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // If AJAX toggle_availability -> return JSON
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle_availability') {
        // Basic CSRF check
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            header('Content-Type: application/json', true, 400);
            echo json_encode(['success' => false, 'message' => 'Invalid request (CSRF).']);
            exit;
        }
        $value = (isset($_POST['value']) && ((string)$_POST['value'] === '1')) ? 1 : 0;
        $stmt = $db->executeQuery("UPDATE drivers SET is_available = ? WHERE id = ?", [$value, $driverId]);
        // no need to check affected rows here; return success

        $db->executeQuery("UPDATE drivers SET is_available = ? WHERE id = ?", [$value, $driverId]);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'is_available' => $value]);
        exit;
    }

    // For non-AJAX actions (accept/advance)
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = 'Invalid request';
        header('Location: driver.php');
        exit;
    }

    $bookingId = (int)($_POST['booking_id'] ?? 0);

    if ($action === 'accept' && $bookingId > 0) {
        // get active vehicle name for this driver
        $stmt = $db->executeQuery(
            "SELECT v.name AS active_vehicle_name
             FROM drivers d
             LEFT JOIN vehicles v ON d.active_vehicle_id = v.id
             WHERE d.id = ? LIMIT 1",
            [$driverId]
        );
        $driverRow = $db->fetch($stmt);
        $activeVehicleName = $driverRow && !empty($driverRow[0]['active_vehicle_name']) ? $driverRow[0]['active_vehicle_name'] : null;

        if (!$activeVehicleName) {
            $_SESSION['flash_error'] = 'Please select an active vehicle in your profile before accepting bookings.';
            header('Location: driver.php');
            exit;
        }

        // ensure driver has no other active booking
        $stmt = $db->executeQuery("SELECT COUNT(*) AS c FROM bookings WHERE driver_id = ? AND tracking_status IN ('accepted','picked_up','in_transit')", [$driverId]);
        $cntRow = $db->fetch($stmt);
        $existingCount = !empty($cntRow) ? (int)$cntRow[0]['c'] : 0;
        if ($existingCount > 0) {
            $_SESSION['flash_error'] = 'You already have an active booking. Finish it before accepting another.';
            header('Location: driver.php');
            exit;
        }
        // mysqli: check if the UPDATE changed any row
        $accepted = (mysqli_stmt_affected_rows($stmt) > 0);

        // Attempt to atomically claim the booking (matching vehicle_type)
        $stmt = $db->executeQuery(
            "UPDATE bookings
             SET driver_id = ?, tracking_status = 'accepted', updated_at = CURRENT_TIMESTAMP()
             WHERE id = ? AND tracking_status = 'pending' AND (driver_id IS NULL OR driver_id = 0) AND vehicle_type = ?",
            [$driverId, $bookingId, $activeVehicleName]
        );

        $affected = mysqli_stmt_affected_rows($stmt);
        if ($affected > 0) {
            $_SESSION['flash_success'] = 'Booking accepted.';
        } else {
            $_SESSION['flash_error'] = 'Booking was already taken, no longer pending, or does not match your active vehicle.';
        }
    } elseif ($action === 'advance' && $bookingId > 0) {
        $stmt = $db->executeQuery("SELECT tracking_status FROM bookings WHERE id = ? AND driver_id = ? LIMIT 1", [$bookingId, $driverId]);
        $row = $db->fetch($stmt);

        if (empty($row)) {
            $_SESSION['flash_error'] = 'Booking not found or not assigned to you.';
        } else {
            $current = $row[0]['tracking_status'];

            $nextMap = [
                'accepted'   => 'picked_up',
                'picked_up'  => 'in_transit',
                'in_transit' => 'delivered',
            ];

            if (!isset($nextMap[$current])) {
                $_SESSION['flash_error'] = 'Cannot advance status from "' . htmlspecialchars($current) . '".';
            } else {
                $next = $nextMap[$current];

                $stmt2 = $db->executeQuery(
                    "UPDATE bookings
                     SET tracking_status = ?, updated_at = CURRENT_TIMESTAMP()
                     WHERE id = ? AND driver_id = ?",
                    [$next, $bookingId, $driverId]
                );

                $updated = mysqli_stmt_affected_rows($stmt2) > 0;
                // mysqli: check if the UPDATE changed any row
                $updated = (mysqli_stmt_affected_rows($stmt2) > 0);

                if ($updated) {
                    $_SESSION['flash_success'] = 'Booking status updated to ' . $next . '.';
                } else {
                    $_SESSION['flash_error'] = 'Failed to update booking status. Try again.';
                }
            }
        }
    }

    header('Location: driver.php');
    exit;
}

/* Fetch driver info (include active vehicle id and name) */
$stmt = $db->executeQuery(
    "SELECT d.first_name, d.is_available, d.active_vehicle_id, v.name AS active_vehicle_name
     FROM drivers d
     LEFT JOIN vehicles v ON d.active_vehicle_id = v.id
     WHERE d.id = ? LIMIT 1",
    [$driverId]
);
$rows = $db->fetch($stmt);

if (empty($rows)) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$driverName = $rows[0]['first_name'];
$isAvailable = (int)$rows[0]['is_available'];
$activeVehicleId = isset($rows[0]['active_vehicle_id']) ? (int)$rows[0]['active_vehicle_id'] : null;
$activeVehicleName = $rows[0]['active_vehicle_name'] ?? null;

/* Load the vehicles the driver owns (driver_vehicles) */
$stmt = $db->executeQuery(
    "SELECT dv.id AS dv_id, dv.vehicle_id, v.name AS vehicle_name, dv.license_plate
     FROM driver_vehicles dv
     JOIN vehicles v ON dv.vehicle_id = v.id
     WHERE dv.driver_id = ?
     ORDER BY v.name ASC",
    [$driverId]
);
$ownedVehicles = $db->fetch($stmt);

/* Only fetch bookings if driver is online */
$pendingBookings = [];
$myBookings = [];

if ($isAvailable === 1) {
    if ($activeVehicleName) {
        $stmtPending = $db->executeQuery(
            "SELECT b.*, u.first_name AS user_first_name, u.last_name AS user_last_name
             FROM bookings b
             JOIN users u ON b.user_id = u.id
             WHERE b.tracking_status = 'pending' AND b.vehicle_type = ?
             ORDER BY b.created_at ASC",
            [$activeVehicleName]
        );
        $pendingBookings = $db->fetch($stmtPending);
    } else {
        $pendingBookings = [];
    }

    $stmtMine = $db->executeQuery(
        "SELECT b.*, u.first_name AS user_first_name, u.last_name AS user_last_name
         FROM bookings b
         JOIN users u ON b.user_id = u.id
         WHERE b.driver_id = ? AND b.tracking_status IN ('accepted','picked_up','in_transit')
         ORDER BY b.created_at ASC",
        [$driverId]
    );
    $myBookings = $db->fetch($stmtMine);
}

/* Helper to check if driver currently has an active booking (to disable accept buttons) */
$hasActiveAssignment = false;
$stmt = $db->executeQuery("SELECT COUNT(*) AS c FROM bookings WHERE driver_id = ? AND tracking_status IN ('accepted','picked_up','in_transit')", [$driverId]);
$cRow = $db->fetch($stmt);
if (!empty($cRow) && isset($cRow[0]['c'])) {
    $hasActiveAssignment = ((int)$cRow[0]['c'] > 0);
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
        :root {
            --brand-yellow: #fce354;
            --brand-dark: #111;
        }

        body {
            background-color: #f4f7f6;
            font-family: 'Inter', sans-serif;
        }

        .status-toggle-card {
            background-color: var(--brand-dark);
            color: white;
            border-radius: 20px;
            padding: 15px 25px;
        }

        .booking-card {
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: white;
        }

        .small-muted {
            font-size: .9rem;
            color: #6c757d;
        }

        .list-container {
            max-height: 60vh;
            overflow-y: auto;
            padding-right: 8px;
        }

        @media (max-width: 767px) {
            .list-container {
                max-height: none;
            }
        }

        .vehicle-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            background: white;
            border: 1px solid #e9ecef;
            margin-right: 8px;
        }

        .vehicle-active {
            border-color: var(--brand-yellow);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>

<body>

    <div class="container py-4">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="fw-bold mb-0">Hello, <?php echo htmlspecialchars($driverName); ?>!</h2>
                <p class="text-muted">Welcome to your driver dashboard.</p>
                <div class="mt-2">
                    <strong>Active vehicle:</strong>
                    <?php if ($activeVehicleName): ?>
                        <span class="vehicle-pill vehicle-active"><?php echo htmlspecialchars($activeVehicleName); ?></span>
                    <?php else: ?>
                        <span class="text-muted small">No active vehicle — set one in your profile to see bookings.</span>
                    <?php endif; ?>
                </div>
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

        <!-- Flash messages -->
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['flash_success']); ?></div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error']); ?></div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-6">
                <h5>Pending Bookings</h5>
                <div class="list-container">
                    <?php if ($isAvailable !== 1): ?>
                        <div class="alert alert-warning">You are currently offline. Toggle <strong>GO ONLINE</strong> to see pending bookings.</div>
                    <?php else: ?>
                        <?php if (!$activeVehicleName): ?>
                            <div class="alert alert-info">You have no active vehicle selected. Go to Profile to add/select a vehicle to see bookings.</div>
                        <?php elseif (empty($pendingBookings)): ?>
                            <div class="alert alert-info">No pending bookings for your active vehicle at the moment.</div>
                        <?php else: ?>
                            <?php foreach ($pendingBookings as $b): ?>
                                <div class="booking-card shadow-sm">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-1">Booking #<?php echo htmlspecialchars($b['id']); ?> — <small class="small-muted">Pending</small></h6>
                                            <div class="small-muted mb-2">Customer: <?php echo htmlspecialchars($b['user_first_name'] . ' ' . $b['user_last_name']); ?> — Amount: ₱<?php echo number_format($b['total_amount'], 2); ?></div>
                                            <div><strong>Pickup:</strong> <span class="small-muted"><?php echo htmlspecialchars(implode(', ', array_filter([$b['pickup_house'], $b['pickup_barangay'], $b['pickup_municipality'], $b['pickup_province']]))); ?></span></div>
                                            <div><strong>Drop:</strong> <span class="small-muted"><?php echo htmlspecialchars(implode(', ', array_filter([$b['drop_house'], $b['drop_barangay'], $b['drop_municipality'], $b['drop_province']]))); ?></span></div>
                                        </div>
                                        <div class="text-end align-self-center">
                                            <form method="post" style="display:inline-block;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="booking_id" value="<?php echo (int)$b['id']; ?>">
                                                <input type="hidden" name="action" value="accept">
                                                <button type="submit" class="btn btn-primary" <?php echo $hasActiveAssignment ? 'disabled title="Finish current assignment before accepting another."' : ''; ?>>Accept</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-6">
                <h5>Your Active Bookings</h5>
                <div class="list-container">
                    <?php if ($isAvailable !== 1): ?>
                        <div class="alert alert-secondary">Your active bookings are hidden while you're offline. Go online to manage them.</div>
                    <?php else: ?>
                        <?php if (empty($myBookings)): ?>
                            <div class="alert alert-info">You have no active bookings assigned.</div>
                        <?php else: ?>
                            <?php foreach ($myBookings as $b):
                                $status = $b['tracking_status'];
                                $btnLabel = next_button_label($status);
                                $statusLabel = ucwords(str_replace('_', ' ', $status));
                            ?>
                                <div class="booking-card shadow-sm">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-1">Booking #<?php echo htmlspecialchars($b['id']); ?> — <small class="small-muted"><?php echo $statusLabel; ?></small></h6>
                                            <div class="small-muted mb-2">Customer: <?php echo htmlspecialchars($b['user_first_name'] . ' ' . $b['user_last_name']); ?> — Amount: ₱<?php echo number_format($b['total_amount'], 2); ?></div>
                                            <div><strong>Pickup:</strong> <span class="small-muted"><?php echo htmlspecialchars(implode(', ', array_filter([$b['pickup_house'], $b['pickup_barangay'], $b['pickup_municipality'], $b['pickup_province']]))); ?></span></div>
                                            <div><strong>Drop:</strong> <span class="small-muted"><?php echo htmlspecialchars(implode(', ', array_filter([$b['drop_house'], $b['drop_barangay'], $b['drop_municipality'], $b['drop_province']]))); ?></span></div>
                                        </div>

                                        <div class="text-end align-self-center">
                                            <?php if ($btnLabel !== null): ?>
                                                <form method="post" style="display:inline-block;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="booking_id" value="<?php echo (int)$b['id']; ?>">
                                                    <input type="hidden" name="action" value="advance">
                                                    <button type="submit" class="btn btn-success"><?php echo htmlspecialchars($btnLabel); ?></button>
                                                </form>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">No actions</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Quick list of driver's owned vehicles and active switching help -->
                <div class="mt-3">
                    <h6 class="fw-bold">Your Vehicles</h6>
                    <?php if (empty($ownedVehicles)): ?>
                        <div class="small text-muted">No vehicles registered. Add vehicles in your <a href="driverProfile.php">profile</a>.</div>
                    <?php else: ?>
                        <?php foreach ($ownedVehicles as $v): ?>
                            <div class="vehicle-pill <?php echo ($v['vehicle_id'] === $activeVehicleId) ? 'vehicle-active' : ''; ?>">
                                <strong><?php echo htmlspecialchars($v['vehicle_name']); ?></strong>
                                <?php if (!empty($v['license_plate'])): ?>
                                    <small class="text-muted"> — <?php echo htmlspecialchars($v['license_plate']); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="small text-muted mt-2">To add vehicles or change active vehicle, go to <a href="driverProfile.php">Profile</a>.</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const onlineSwitch = document.getElementById('onlineSwitch');
            if (!onlineSwitch) return;

            onlineSwitch.addEventListener('change', async function() {
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
                        headers: {
                            'Accept': 'application/json'
                        }
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