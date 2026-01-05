<?php
session_start();
require_once _DIR_ . '/../api/classes/Database.php';

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
function next_button_label($status) {
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

        $db->executeQuery("UPDATE drivers SET is_available = ? WHERE id = ?", [$value, $driverId]);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'is_available' => $value]);
        exit;
    }

    // For non-AJAX actions (accept/advance) keep original redirection behaviour
    // Basic CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = 'Invalid request';
        header('Location: driver.php');
        exit;
    }

    $bookingId = (int)($_POST['booking_id'] ?? 0);

    if ($action === 'accept' && $bookingId > 0) {
        // Attempt to atomically claim the booking (only if still pending & unassigned)
        $stmt = $db->executeQuery(
            "UPDATE bookings
             SET driver_id = ?, tracking_status = 'accepted', updated_at = CURRENT_TIMESTAMP()
             WHERE id = ? AND tracking_status = 'pending' AND (driver_id IS NULL OR driver_id = 0)",
            [$driverId, $bookingId]
        );

        // mysqli: check if the UPDATE changed any row
        $accepted = (mysqli_stmt_affected_rows($stmt) > 0);

        if ($accepted) {
            $_SESSION['flash_success'] = 'Booking accepted.';
        } else {
            $_SESSION['flash_error'] = 'Booking was already taken by another driver or is no longer pending.';
        }
    } elseif ($action === 'advance' && $bookingId > 0) {
        // Fetch current status ensuring the booking belongs to this driver
        $stmt = $db->executeQuery(
            "SELECT tracking_status FROM bookings WHERE id = ? AND driver_id = ? LIMIT 1",
            [$bookingId, $driverId]
        );
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

/* Fetch driver info */
$stmt = $db->executeQuery("SELECT first_name, is_available FROM drivers WHERE id = ? LIMIT 1", [$driverId]);
$rows = $db->fetch($stmt);

if (empty($rows)) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$driverName = $rows[0]['first_name'];
$isAvailable = (int)$rows[0]['is_available'];

/* Only fetch bookings if driver is online */
$pendingBookings = [];
$myBookings = [];

if ($isAvailable === 1) {
    /* Fetch pending bookings (unassigned, pending) */
    $stmtPending = $db->executeQuery(
        "SELECT b.*, u.first_name AS user_first_name, u.last_name AS user_last_name
         FROM bookings b
         JOIN users u ON b.user_id = u.id
         WHERE b.tracking_status = 'pending'
         ORDER BY b.created_at ASC"
    );
    $pendingBookings = $db->fetch($stmtPending);

    /* Fetch active bookings assigned to this driver */
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
        .list-container { max-height: 60vh; overflow-y: auto; padding-right: 8px; }
        @media (max-width: 767px) {
            .list-container { max-height: none; }
        }
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
                    <?php if (empty($pendingBookings)): ?>
                        <div class="alert alert-info">No pending bookings at the moment.</div>
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
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const onlineSwitch = document.getElementById('onlineSwitch');
    if (!onlineSwitch) return;

    onlineSwitch.addEventListener('change', async function () {
        const checked = this.checked ? 1 : 0;
        // disable during request
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
                // reload to show/hide bookings
                location.reload();
            } else {
                alert('Failed to change online status: ' + (json?.message ?? 'Unknown error'));
                // revert checkbox if failed
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