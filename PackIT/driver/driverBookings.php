<?php
session_start();
require_once __DIR__ . '/../api/classes/Database.php';
require_once __DIR__ . '/../frontend/components/autorefresh.php';

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

/* Handle advance status */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = 'Invalid request';
        header('Location: driverBookings.php');
        exit;
    }

    $action = $_POST['action'] ?? '';
    $bookingId = (int)($_POST['booking_id'] ?? 0);

    if ($action === 'advance' && $bookingId > 0) {
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

                $updated = (mysqli_stmt_affected_rows($stmt2) > 0);

                if ($updated) {
                    $_SESSION['flash_success'] = 'Booking status updated to ' . $next . '.';
                } else {
                    $_SESSION['flash_error'] = 'Failed to update booking status. Try again.';
                }
            }
        }
    }

    header('Location: driverBookings.php');
    exit;
}

/* Fetch driver name */
$stmt = $db->executeQuery("SELECT first_name FROM drivers WHERE id = ? LIMIT 1", [$driverId]);
$rows = $db->fetch($stmt);
$driverName = !empty($rows) ? $rows[0]['first_name'] : 'Driver';

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
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Driver Bookings | PackIT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .booking-card { border-radius: 12px; padding: 1rem; margin-bottom: 1rem; background: white; }
        .small-muted { font-size: .9rem; color: #6c757d; }
        .list-container { max-height: 75vh; overflow-y: auto; padding-right: 8px; }
    </style>
</head>
<body>

<?php include __DIR__ . "/../frontend/components/driverNavbar.php"; ?>

<div class="container py-4">
    <h3 class="fw-bold mb-1">Your Active Deliveries</h3>
    <p class="text-muted mb-4">Manage the booking(s) you are currently delivering.</p>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['flash_success']); ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error']); ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="list-container">
        <?php if (empty($myBookings)): ?>
            <div class="alert alert-info">You have no active bookings right now.</div>
        <?php else: ?>
            <?php foreach ($myBookings as $b):
                $status = $b['tracking_status'];
                $btnLabel = next_button_label($status);
                $statusLabel = ucwords(str_replace('_', ' ', $status));
            ?>
                <div class="booking-card shadow-sm">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-1">
                                Booking #<?php echo htmlspecialchars($b['id']); ?>
                                — <small class="small-muted"><?php echo htmlspecialchars($statusLabel); ?></small>
                            </h6>
                            <div class="small-muted mb-2">
                                Customer: <?php echo htmlspecialchars($b['user_first_name'] . ' ' . $b['user_last_name']); ?>
                                — Amount: ₱<?php echo number_format($b['total_amount'], 2); ?>
                            </div>
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
    </div>
</div>

<?php include __DIR__ . "/../frontend/components/driverFooter.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>