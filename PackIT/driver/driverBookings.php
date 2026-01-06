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

function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money($n): string { return number_format((float)$n, 2); }
function status_label(string $status): string { return strtoupper(str_replace('_', ' ', $status)); }
function fmtDims($l, $w, $h): string {
    $fmt = fn($x) => rtrim(rtrim(number_format((float)$x, 1), '0'), '.');
    return $fmt($l) . " x " . $fmt($w) . " x " . $fmt($h) . " m";
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
            $current = (string)$row[0]['tracking_status'];
            $nextMap = [
                'accepted'   => 'picked_up',
                'picked_up'  => 'in_transit',
                'in_transit' => 'delivered',
            ];

            if (!isset($nextMap[$current])) {
                $_SESSION['flash_error'] = 'Cannot advance status from "' . h($current) . '".';
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

                    if ($next === 'delivered') {
                        header('Location: driver.php');
                        exit;
                    }
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

/* Fetch active bookings assigned to this driver (includes user contact_number) */
$stmtMine = $db->executeQuery(
    "SELECT b.*,
            u.first_name AS user_first_name,
            u.last_name AS user_last_name,
            u.contact_number AS user_contact_number,
            u.email AS user_email
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
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root { --secondary-teal: #203a43; --card-border: #203a43; }

        body {
            background-color: #f4f6f8;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-content { flex: 1; padding: 3rem 1rem; }

        .main-container {
            background: #ffffff;
            border-radius: 15px;
            border: 2px solid var(--card-border);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            min-height: 500px;
        }

        .status-badge-green, .status-badge-gray {
            font-weight: 700;
            font-size: 0.8rem;
            padding: 6px 16px;
            border-radius: 20px;
            display: inline-block;
        }
        .status-badge-green { background-color: #c9f29d; color: #2c5206; }
        .status-badge-gray  { background-color: #e9ecef; color: #495057; }

        .detail-card {
            background-color: #f8f9fa;
            border-radius: 15px;
            border: 1px solid #e9ecef;
            padding: 1.25rem;
        }

        .kv {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px dashed rgba(0,0,0,0.08);
        }
        .kv:last-child { border-bottom: none; }
        .kv .k {
            color: #6c757d;
            font-size: .85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .kv .v { color: #212529; font-weight: 700; text-align: right; }

        .empty-state-container {
            text-align: center;
            padding: 4rem 1rem;
            color: #6c757d;
        }
        .empty-state-img { width: 150px; margin-bottom: 1.5rem; opacity: 0.8; }

        .list-scroll { max-height: 70vh; overflow: auto; padding-right: .25rem; }
    </style>
</head>
<body>

<?php include __DIR__ . "/../frontend/components/driverNavbar.php"; ?>

<div class="main-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="main-container p-4 p-md-5">

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                        <div>
                            <h4 class="fw-bold mb-1" style="color: var(--secondary-teal);">
                                <span class="material-symbols-outlined align-middle me-2">inventory_2</span>
                                Active Booking Details
                            </h4>
                            <div class="text-muted small">
                                Hi <?= h($driverName) ?> — update status and view complete booking details.
                            </div>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="driver.php" class="btn btn-outline-dark fw-bold px-3 shadow-sm text-uppercase">
                                <i class="bi bi-arrow-left me-1"></i> Dashboard
                            </a>
                        </div>
                    </div>

                    <?php if (!empty($_SESSION['flash_success'])): ?>
                        <div class="alert alert-success"><?= h($_SESSION['flash_success']); ?></div>
                        <?php unset($_SESSION['flash_success']); ?>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['flash_error'])): ?>
                        <div class="alert alert-danger"><?= h($_SESSION['flash_error']); ?></div>
                        <?php unset($_SESSION['flash_error']); ?>
                    <?php endif; ?>

                    <?php if (empty($myBookings)): ?>
                        <div class="empty-state-container">
                            <img src="../assets/box.png" alt="No bookings" class="empty-state-img">
                            <h4 class="fw-bold text-dark">No Active Booking</h4>
                            <p class="mb-0">Accept a booking from the dashboard to start delivering.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-scroll">
                            <?php foreach ($myBookings as $b):
                                $status = (string)($b['tracking_status'] ?? '');
                                $btnLabel = next_button_label($status);

                                $pickupFull = implode(', ', array_filter([
                                    $b['pickup_house'] ?? null,
                                    $b['pickup_barangay'] ?? null,
                                    $b['pickup_municipality'] ?? null,
                                    $b['pickup_province'] ?? null,
                                ]));

                                $dropFull = implode(', ', array_filter([
                                    $b['drop_house'] ?? null,
                                    $b['drop_barangay'] ?? null,
                                    $b['drop_municipality'] ?? null,
                                    $b['drop_province'] ?? null,
                                ]));

                                $customerName = trim(($b['user_first_name'] ?? '') . ' ' . ($b['user_last_name'] ?? ''));
                                $customerCp = (string)($b['user_contact_number'] ?? '');

                                $pkgQty = (int)($b['package_quantity'] ?? 1);
                                $pkgDesc = (string)($b['package_desc'] ?? '');
                                $pkgType = (string)($b['package_type'] ?? '');
                                $dims = fmtDims($b['size_length_m'] ?? 0, $b['size_width_m'] ?? 0, $b['size_height_m'] ?? 0);
                            ?>
                                <div class="detail-card mb-4">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                                        <div>
                                            <h5 class="fw-bold mb-1" style="color: var(--secondary-teal);">
                                                Booking #<?= (int)$b['id'] ?>
                                            </h5>
                                            <div class="text-muted small">
                                                Customer: <strong><?= h($customerName ?: '—') ?></strong>
                                                <?php if ($customerCp !== ''): ?>
                                                    • CP: <strong><?= h($customerCp) ?></strong>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                            <span class="status-badge-gray">VEHICLE: <?= h($b['vehicle_type'] ?? '—') ?></span>
                                            <span class="status-badge-green"><?= h(status_label($status)) ?></span>
                                        </div>
                                    </div>

                                    <hr class="my-3 text-muted opacity-25">

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="detail-card">
                                                <div class="fw-bold mb-2"><i class="bi bi-box-seam me-1"></i> Package</div>
                                                <div class="kv"><div class="k">Quantity</div><div class="v"><?= (int)$pkgQty ?></div></div>
                                                <div class="kv"><div class="k">Description</div><div class="v"><?= h($pkgDesc !== '' ? $pkgDesc : '—') ?></div></div>
                                                <div class="kv"><div class="k">Type</div><div class="v"><?= h($pkgType !== '' ? $pkgType : '—') ?></div></div>
                                                <div class="kv"><div class="k">Max KG</div><div class="v"><?= (int)($b['max_kg'] ?? 0) ?> kg</div></div>
                                                <div class="kv"><div class="k">Size</div><div class="v"><?= h($dims) ?></div></div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="detail-card">
                                                <div class="fw-bold mb-2"><i class="bi bi-geo-alt me-1"></i> Pickup</div>
                                                <div class="text-muted small mb-2"><?= h($pickupFull ?: '—') ?></div>
                                                <div class="kv"><div class="k">Pickup Contact</div><div class="v"><?= h(($b['pickup_contact_name'] ?? '—') . ' • ' . ($b['pickup_contact_number'] ?? '—')) ?></div></div>
                                                <div class="kv"><div class="k">Province</div><div class="v"><?= h($b['pickup_province'] ?? '—') ?></div></div>
                                                <div class="kv"><div class="k">Municipality</div><div class="v"><?= h($b['pickup_municipality'] ?? '—') ?></div></div>
                                                <div class="kv"><div class="k">Barangay</div><div class="v"><?= h($b['pickup_barangay'] ?? '—') ?></div></div>
                                                <div class="kv"><div class="k">House</div><div class="v"><?= h($b['pickup_house'] ?? '—') ?></div></div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="detail-card">
                                                <div class="fw-bold mb-2"><i class="bi bi-flag me-1"></i> Drop-off</div>
                                                <div class="text-muted small mb-2"><?= h($dropFull ?: '—') ?></div>
                                                <div class="kv"><div class="k">Recipient</div><div class="v"><?= h(($b['drop_contact_name'] ?? '—') . ' • ' . ($b['drop_contact_number'] ?? '—')) ?></div></div>
                                                <div class="kv"><div class="k">Province</div><div class="v"><?= h($b['drop_province'] ?? '—') ?></div></div>
                                                <div class="kv"><div class="k">Municipality</div><div class="v"><?= h($b['drop_municipality'] ?? '—') ?></div></div>
                                                <div class="kv"><div class="k">Barangay</div><div class="v"><?= h($b['drop_barangay'] ?? '—') ?></div></div>
                                                <div class="kv"><div class="k">House</div><div class="v"><?= h($b['drop_house'] ?? '—') ?></div></div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="detail-card">
                                                <div class="fw-bold mb-2"><i class="bi bi-receipt me-1"></i> Fare & Payment</div>

                                                <div class="kv"><div class="k">Base Amount</div><div class="v">₱ <?= h(money($b['base_amount'] ?? 0)) ?></div></div>
                                                <div class="kv"><div class="k">Distance Amount</div><div class="v">₱ <?= h(money($b['distance_amount'] ?? 0)) ?></div></div>
                                                <div class="kv"><div class="k">Total Amount</div><div class="v text-warning">₱ <?= h(money($b['total_amount'] ?? 0)) ?></div></div>

                                                <div class="kv"><div class="k">Payment Status</div><div class="v"><?= h($b['payment_status'] ?? '—') ?></div></div>
                                                <div class="kv"><div class="k">Payment Method</div><div class="v"><?= h($b['payment_method'] ?? '—') ?></div></div>

                                                <div class="kv"><div class="k">Created</div><div class="v"><?= h($b['created_at'] ?? '—') ?></div></div>
                                                <div class="kv"><div class="k">Updated</div><div class="v"><?= h($b['updated_at'] ?? '—') ?></div></div>

                                                <div class="mt-3 text-md-end">
                                                    <?php if ($btnLabel !== null): ?>
                                                        <form method="post" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                                                            <input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
                                                            <input type="hidden" name="action" value="advance">
                                                            <button type="submit" class="btn btn-warning fw-bold px-4 py-2 shadow-sm text-uppercase w-100 w-md-auto">
                                                                <?= h($btnLabel) ?>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="status-badge-gray">NO ACTIONS</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../frontend/components/driverFooter.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>