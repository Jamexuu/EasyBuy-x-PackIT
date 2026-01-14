<?php
session_start();
require_once __DIR__ . '/../api/classes/Database.php';
require_once __DIR__ . '/../frontend/components/autorefresh.php';
require_once __DIR__ . '/../api/sms/SmsNotificationService.php'; // Import the SMS service
require_once __DIR__ . '/../vendor/autoload.php'; // adjust path if vendor is elsewhere
Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->safeLoad(); // loads PackIT/.env

// Define $action to avoid undefined variable issues
$action = $_POST['action'] ?? $_GET['action'] ?? null;

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
            header('Location: driverBookings.php');
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

            // ---------- SMS notification on ACCEPT ----------
            $stmtBooking = $db->executeQuery(
                "SELECT b.pickup_contact_number, d.first_name AS driver_name
                 FROM bookings b
                 JOIN drivers d ON b.driver_id = d.id
                 WHERE b.id = ? LIMIT 1",
                [$bookingId]
            );
            $bookingDetails = $db->fetch($stmtBooking)[0] ?? null;

            if ($bookingDetails && !empty($bookingDetails['pickup_contact_number'])) {
                $smsService = new SmsNotificationService();

                $smsService->notify(
                    [$bookingDetails['pickup_contact_number']],
                    $smsService->getTemplate('booking_accepted', [
                        'booking_id'  => $bookingId,
                        'driver_name' => $bookingDetails['driver_name'] ?? '',
                    ]),
                    [
                        'booking_id' => $bookingId,
                        'driver_id'  => $driverId,
                        'status'     => 'booking_accepted',
                    ]
                );
            }
            // ------------------------------------------------------

            header('Location: driverBookings.php');
            exit;
        } else {
            $_SESSION['flash_error'] = 'Booking was already taken, no longer pending, or does not match your active vehicle.';
        }
    }

    header('Location: driver.php');
    exit;
}

/* Fetch driver info */
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
    header("Location: index.php");
    exit;
}

$isAvailable = (int)($rows[0]['is_available'] ?? 0);

/* Use vehicle_type if present; else fallback to vehicles.name; else empty string */
$activeVehicleName = '';
if (!empty($rows[0]['vehicle_type'])) {
    $activeVehicleName = (string)$rows[0]['vehicle_type'];
} elseif (!empty($rows[0]['active_vehicle_name'])) {
    $activeVehicleName = (string)$rows[0]['active_vehicle_name'];
}

/* Active booking check */
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
        "SELECT b.*,
                u.first_name AS user_first_name,
                u.last_name AS user_last_name,
                u.contact_number AS user_contact_number,
                u.email AS user_email
         FROM bookings b
         JOIN users u ON b.user_id = u.id
         WHERE b.tracking_status = 'pending'
           AND b.vehicle_type = ?
         ORDER BY b.created_at ASC",
        [$activeVehicleName]
    );
    $pendingBookings = $db->fetch($stmtPending);
}

/* Helpers */
function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money($n): string { return number_format((float)$n, 2); }
function badgeStatusLabel(int $isAvailable): string { return $isAvailable === 1 ? 'ONLINE' : 'OFFLINE'; }

function fmtDims($l, $w, $h): string {
    $fmt = fn($x) => rtrim(rtrim(number_format((float)$x, 1), '0'), '.');
    return $fmt($l) . " x " . $fmt($w) . " m";
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Driver Dashboard | PackIT</title>

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

        .order-card-item {
            background-color: #f8f9fa;
            border-radius: 15px;
            border: 1px solid #e9ecef;
            transition: transform 0.2s;
        }
        .order-card-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .status-badge-green, .status-badge-gray, .status-badge-red {
            font-weight: 700;
            font-size: 0.8rem;
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
        }
        .status-badge-green { background-color: #c9f29d; color: #2c5206; }
        .status-badge-gray  { background-color: #e9ecef; color: #495057; }
        .status-badge-red   { background-color: #ffd1d1; color: #842029; }

        .empty-state-container {
            text-align: center;
            padding: 4rem 1rem;
            color: #6c757d;
        }
        .empty-state-img { width: 150px; margin-bottom: 1.5rem; opacity: 0.8; }

        .list-scroll { max-height: 68vh; overflow: auto; padding-right: .25rem; }
        .form-check-input { cursor: pointer; }

        .kv {
            display:flex;
            justify-content:space-between;
            gap:12px;
            padding:6px 0;
            border-bottom: 1px dashed rgba(0,0,0,0.08);
        }
        .kv:last-child { border-bottom: none; }
        .kv .k { color:#6c757d; font-size:.78rem; font-weight:800; text-transform:uppercase; letter-spacing:.04em; }
        .kv .v { font-weight:700; text-align:right; }

        .btn-link-lite {
            padding: 0;
            border: 0;
            background: transparent;
            color: #0d6efd;
            font-weight: 700;
            text-decoration: none;
        }
        .btn-link-lite:hover { text-decoration: underline; }
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
                        <h4 class="fw-bold mb-0" style="color: var(--secondary-teal);">
                            <span class="material-symbols-outlined align-middle me-2">local_shipping</span>
                            Pending Bookings
                        </h4>

                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <?php if ($isAvailable === 1): ?>
                                <span class="status-badge-green"><?= h(badgeStatusLabel($isAvailable)) ?></span>
                            <?php else: ?>
                                <span class="status-badge-red"><?= h(badgeStatusLabel($isAvailable)) ?></span>
                            <?php endif; ?>

                            <span class="status-badge-gray">
                                ACTIVE VEHICLE: <?= $activeVehicleName !== '' ? h($activeVehicleName) : 'NOT SET' ?>
                            </span>

                            <div class="d-flex align-items-center gap-2 ms-md-2">
                                <span class="small fw-bold text-muted">GO ONLINE</span>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="onlineSwitch"
                                        <?= $isAvailable === 1 ? 'checked' : '' ?>
                                        style="width: 2.5em; height: 1.25em;">
                                </div>
                            </div>
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

                    <?php if ($hasActiveAssignment): ?>
                        <div class="empty-state-container">
                            <img src="../assets/box.png" alt="Active booking" class="empty-state-img">
                            <h4 class="fw-bold text-dark">You Have an Active Booking</h4>
                            <p class="mb-3">Finish your current booking before accepting a new one.</p>
                            <a href="driverBookings.php" class="btn btn-warning fw-bold px-4 py-2 shadow-sm text-uppercase">
                                Go to Active Booking
                            </a>
                        </div>

                    <?php elseif ($isAvailable !== 1): ?>
                        <div class="empty-state-container">
                            <img src="../assets/box.png" alt="Offline" class="empty-state-img">
                            <h4 class="fw-bold text-dark">You are Offline</h4>
                            <p class="mb-0">Turn <strong>GO ONLINE</strong> on to start receiving pending bookings.</p>
                        </div>

                    <?php elseif ($activeVehicleName === ''): ?>
                        <div class="empty-state-container">
                            <img src="../assets/box.png" alt="No active vehicle" class="empty-state-img">
                            <h4 class="fw-bold text-dark">No Active Vehicle</h4>
                            <p class="mb-3">Set your active vehicle in your profile so we can show bookings that match your vehicle type.</p>
                            <a href="driverProfile.php" class="btn btn-outline-dark fw-bold px-4 py-2 shadow-sm text-uppercase">
                                Go to Profile
                            </a>
                        </div>

                    <?php elseif (empty($pendingBookings)): ?>
                        <div class="empty-state-container">
                            <img src="../assets/box.png" alt="No bookings" class="empty-state-img">
                            <h4 class="fw-bold text-dark">No Pending Bookings</h4>
                            <p class="mb-0">There are no pending bookings for your active vehicle right now. Please check again later.</p>
                        </div>

                    <?php else: ?>
                        <div class="list-scroll">
                            <?php foreach ($pendingBookings as $b): ?>
                                <?php
                                    $id = (int)($b['id'] ?? 0);
                                    $collapseId = "details_" . $id;

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

                                    $pkgQty = (int)($b['package_quantity'] ?? 1);
                                    $pkgDesc = (string)($b['package_desc'] ?? '');
                                    $pkgType = (string)($b['package_type'] ?? '');
                                    $dims = fmtDims($b['size_length_m'] ?? 0, $b['size_width_m'] ?? 0, $b['size_height_m'] ?? 0);

                                    $pickupContact = trim((string)($b['pickup_contact_name'] ?? '')) !== ''
                                        ? (string)$b['pickup_contact_name'] . ' • ' . (string)($b['pickup_contact_number'] ?? '')
                                        : '—';

                                    $dropContact = trim((string)($b['drop_contact_name'] ?? '')) !== ''
                                        ? (string)$b['drop_contact_name'] . ' • ' . (string)($b['drop_contact_number'] ?? '')
                                        : '—';
                                ?>
                                <div class="order-card-item p-4 mb-4">
                                    <!-- Compact header -->
                                    <div class="row align-items-center">
                                        <div class="col-md-2 text-center mb-3 mb-md-0">
                                            <div class="bg-white rounded p-3 d-inline-block shadow-sm">
                                                <img src="../assets/box.png" alt="Package" style="width: 40px; height: 40px;">
                                            </div>
                                        </div>

                                        <div class="col-md-7 mb-3 mb-md-0">
                                            <div class="mb-2 d-flex flex-wrap gap-2">
                                                <span class="status-badge-gray">PENDING • ID <?= $id ?></span>
                                                <span class="status-badge-gray">VEHICLE: <?= h($b['vehicle_type'] ?? $activeVehicleName) ?></span>
                                                <span class="status-badge-gray">QTY: <?= (int)$pkgQty ?></span>
                                            </div>

                                            <h6 class="fw-bold mb-1 text-dark">
                                                <?= h(($b['user_first_name'] ?? '') . ' ' . ($b['user_last_name'] ?? '')) ?>
                                            </h6>

                                            <div class="small text-muted">
                                                <strong>Route:</strong> <?= h($pickupFull) ?> → <?= h($dropFull) ?>
                                            </div>

                                            <div class="mt-2 small">
                                                <button class="btn-link-lite" type="button" data-bs-toggle="collapse" data-bs-target="#<?= h($collapseId) ?>" aria-expanded="false" aria-controls="<?= h($collapseId) ?>">
                                                    Show details
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-md-3 text-md-end text-start">
                                            <div class="fw-bold text-warning small text-uppercase" style="letter-spacing: 1px;">
                                                ₱ <?= h(money($b['total_amount'] ?? 0)) ?>
                                            </div>
                                            <div class="small text-muted mt-1">
                                                Base: ₱<?= h(money($b['base_amount'] ?? 0)) ?><br>
                                                Distance: ₱<?= h(money($b['distance_amount'] ?? 0)) ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Collapsible details -->
                                    <div class="collapse mt-3" id="<?= h($collapseId) ?>">
                                        <div class="bg-white rounded-3 p-3 border">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-6">
                                                    <div class="fw-bold small mb-2">Package</div>
                                                    <div class="kv"><div class="k">Quantity</div><div class="v"><?= (int)$pkgQty ?></div></div>
                                                    <div class="kv"><div class="k">Description</div><div class="v"><?= h($pkgDesc !== '' ? $pkgDesc : '—') ?></div></div>
                                                    <div class="kv"><div class="k">Type</div><div class="v"><?= h($pkgType !== '' ? $pkgType : '—') ?></div></div>
                                                    <div class="kv"><div class="k">Max KG</div><div class="v"><?= (int)($b['max_kg'] ?? 0) ?> kg</div></div>
                                                    <div class="kv"><div class="k">Size</div><div class="v"><?= h($dims) ?></div></div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="fw-bold small mb-2">Contacts</div>
                                                    <div class="kv"><div class="k">User CP</div><div class="v"><?= h($b['user_contact_number'] ?? '—') ?></div></div>
                                                    <div class="kv"><div class="k">Pickup</div><div class="v"><?= h($pickupContact !== '' ? $pickupContact : '—') ?></div></div>
                                                    <div class="kv"><div class="k">Recipient</div><div class="v"><?= h($dropContact !== '' ? $dropContact : '—') ?></div></div>

                                                    <div class="fw-bold small mt-3 mb-2">Meta</div>
                                                    <div class="kv"><div class="k">Payment</div><div class="v"><?= h(($b['payment_status'] ?? '—') . ' / ' . ($b['payment_method'] ?? '—')) ?></div></div>
                                                    <div class="kv"><div class="k">Created</div><div class="v"><?= h($b['created_at'] ?? '—') ?></div></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-3 text-muted opacity-25">

                                    <div class="row align-items-center">
                                        <div class="col-md-6 text-muted small">
                                            Tip: Review details before accepting.
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                                                <input type="hidden" name="booking_id" value="<?= $id ?>">
                                                <input type="hidden" name="action" value="accept">
                                                <button type="submit" class="btn btn-warning fw-bold px-4 shadow-sm text-uppercase">
                                                    ACCEPT
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="mt-5">
                        <h4 class="fw-bold mb-4" style="color: var(--secondary-teal);">
                            <span class="material-symbols-outlined align-middle me-2">shopping_bag</span>
                            EasyBuy Orders
                        </h4>
                        <div class="list-scroll" id="easybuyOrdersContainer">
                            <div class="empty-state-container">
                                <div class="spinner-border text-warning" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 mb-0">Loading EasyBuy orders...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Accept Order Confirmation Modal -->
<div class="modal fade" id="acceptOrderModal" tabindex="-1" aria-labelledby="acceptOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="acceptOrderModalLabel">Accept EasyBuy Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center py-3">
                    <div class="mb-3">
                        <i class="bi bi-bag-check" style="font-size: 3rem; color: var(--secondary-teal);"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Accept Order #<span id="modalOrderId"></span>?</h6>
                    <p class="text-muted mb-0">You're about to accept this EasyBuy order. The order will be moved to your active bookings.</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning fw-bold" id="confirmAcceptBtn">Accept Order</button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../frontend/components/driverFooter.php"; ?>

<script>
    var $easybuyIP = $_ENV['EASYBUY_IP'] ?? 'localhost';
async function fetchEasyBuyOrders() {
    const container = document.getElementById('easybuyOrdersContainer');
    
    try {
        const responseOrders = await fetch(`http://${easybuyIP}/EasyBuy-x-PackIT/EasyBuy/api/getAllOrders.php`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        });

        if (!responseOrders.ok) {
            throw new Error('Network response was not ok');
        }

        const orders = await responseOrders.json();

        // Filter out accepted orders and cancelled orders
        const excludedStatuses = ['picked up', 'in transit', 'order arrived', 'cancelled', 'canceled'];
        const pendingOrders = orders.filter(order => {
            const status = (order.status || '').toLowerCase();
            return !excludedStatuses.includes(status);
        });

        if (!pendingOrders || pendingOrders.length === 0) {
            container.innerHTML = `
                <div class="empty-state-container">
                    <img src="../assets/box.png" alt="No orders" class="empty-state-img">
                    <h5 class="fw-bold text-dark">No EasyBuy Orders</h5>
                    <p class="mb-0">There are no orders available at the moment.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = '';

        pendingOrders.forEach((order, index) => {
            const orderId = order.orderID || 'N/A';
            const collapseId = 'easybuy_' + orderId;
            const customerName = (order.firstName || '') + ' ' + (order.lastName || '');
            const totalAmount = parseFloat(order.totalAmount || 0).toFixed(2);
            const status = order.status || 'pending';
            const paymentMethod = order.paymentMethod || 'N/A';
            const createdAt = order.orderDate || 'N/A';
            
            // Get shipping address from address object
            const addr = order.address || {};
            const shippingAddress = [
                addr.houseNumber,
                addr.street,
                addr.lot,
                addr.block,
                addr.barangay,
                addr.city,
                addr.province
            ].filter(Boolean).join(', ') || 'N/A';

            // Count items
            const itemCount = order.items ? order.items.length : 0;
            
            // Build items HTML
            let itemsHTML = '';
            if (order.items && order.items.length > 0) {
                itemsHTML = order.items.map(item => `
                    <div class="kv">
                        <div class="k">${item.quantity}x ${item.product_name || 'Product'}</div>
                        <div class="v">₱${parseFloat(item.product_price || 0).toFixed(2)}</div>
                    </div>
                `).join('');
            } else {
                itemsHTML = '<div class="text-muted small">No items</div>';
            }

            const orderCard = `
                <div class="order-card-item p-4 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <div class="bg-white rounded p-3 d-inline-block shadow-sm">
                                <img src="../assets/box.png" alt="Package" style="width: 40px; height: 40px;">
                            </div>
                        </div>

                        <div class="col-md-7 mb-3 mb-md-0">
                            <div class="mb-2 d-flex flex-wrap gap-2">
                                <span class="status-badge-gray">${status.toUpperCase()} • ID ${orderId}</span>
                                <span class="status-badge-gray">ITEMS: ${itemCount}</span>
                                <span class="status-badge-gray">EASYBUY</span>
                            </div>

                            <h6 class="fw-bold mb-1 text-dark">
                                ${customerName || 'Customer'}
                            </h6>

                            <div class="small text-muted">
                                <strong>Delivery to:</strong> ${shippingAddress}
                            </div>

                            <div class="mt-2 small">
                                <button class="btn-link-lite" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#${collapseId}" aria-expanded="false" 
                                        aria-controls="${collapseId}">
                                    Show details
                                </button>
                            </div>
                        </div>

                        <div class="col-md-3 text-md-end text-start">
                            <div class="fw-bold text-warning small text-uppercase" style="letter-spacing: 1px;">
                                ₱ ${totalAmount}
                            </div>
                            <div class="small text-muted mt-1">
                                Payment: ${paymentMethod}
                            </div>
                        </div>
                    </div>

                    <!-- Collapsible details -->
                    <div class="collapse mt-3" id="${collapseId}">
                        <div class="bg-white rounded-3 p-3 border">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <div class="fw-bold small mb-2">Order Items</div>
                                    ${itemsHTML}
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="fw-bold small mb-2">Contact & Delivery</div>
                                    <div class="kv">
                                        <div class="k">Email</div>
                                        <div class="v">${order.userEmail || 'N/A'}</div>
                                    </div>
                                    <div class="kv">
                                        <div class="k">Phone</div>
                                        <div class="v">${order.contactNumber || 'N/A'}</div>
                                    </div>
                                    <div class="kv">
                                        <div class="k">Address</div>
                                        <div class="v">${shippingAddress}</div>
                                    </div>

                                    <div class="fw-bold small mt-3 mb-2">Order Info</div>
                                    <div class="kv">
                                        <div class="k">Payment</div>
                                        <div class="v">${paymentMethod}</div>
                                    </div>
                                    <div class="kv">
                                        <div class="k">Created</div>
                                        <div class="v">${createdAt}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3 text-muted opacity-25">

                    <div class="row align-items-center">
                        <div class="col-md-6 text-muted small">
                            Tip: Review details before accepting.
                        </div>
                        <div class="col-md-6 text-md-end">
                            <button class="btn btn-warning fw-bold px-4 shadow-sm text-uppercase" 
                                    onclick="acceptEasyBuyOrder(${orderId})">
                                ACCEPT ORDER
                            </button>
                        </div>
                    </div>
                </div>
            `;

            container.innerHTML += orderCard;
        });

    } catch (err) {
        console.error('Error fetching EasyBuy orders:', err);
        container.innerHTML = `
            <div class="empty-state-container">
                <img src="../assets/box.png" alt="Error" class="empty-state-img">
                <h5 class="fw-bold text-dark">Failed to load EasyBuy orders</h5>
                <p class="mb-0">Please try again later.</p>
            </div>
        `;
    }
}

let currentOrderId = null;
let currentAcceptButton = null;

function acceptEasyBuyOrder(orderId) {
    currentOrderId = orderId;
    currentAcceptButton = event.target;
    
    // Update modal with order ID
    document.getElementById('modalOrderId').textContent = orderId;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('acceptOrderModal'));
    modal.show();
}

async function confirmAcceptOrder() {
    if (!currentOrderId || !currentAcceptButton) return;
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('acceptOrderModal'));
    const confirmBtn = document.getElementById('confirmAcceptBtn');
    
    // Disable button and show loading
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Accepting...';
    currentAcceptButton.disabled = true;
    currentAcceptButton.textContent = 'ACCEPTING...';

    try {
        const response = await fetch(`http://${easybuyIP}/EasyBuy-x-PackIT/EasyBuy/api/updateOrderStatusByDriver.php`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                orderId: currentOrderId,
                driverId: <?= $driverId ?>,
                newStatus: 'picked up'
            })
        });

        const result = await response.json();

        if (response.ok && result.success) {
            // Close modal and redirect
            modal.hide();
            window.location.href = 'driverBookings.php';
        } else {
            console.error('Failed to accept order:', result.error);
            modal.hide();
            alert('Failed to accept order: ' + (result.error || 'Unknown error'));
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Accept Order';
            currentAcceptButton.disabled = false;
            currentAcceptButton.textContent = 'ACCEPT ORDER';
        }
    } catch (err) {
        console.error('Error accepting order:', err);
        modal.hide();
        alert('Error accepting order. Please try again.');
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Accept Order';
        currentAcceptButton.disabled = false;
        currentAcceptButton.textContent = 'ACCEPT ORDER';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Fetch EasyBuy orders on page load
    fetchEasyBuyOrders();
    
    // Setup modal confirm button
    const confirmBtn = document.getElementById('confirmAcceptBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', confirmAcceptOrder);
    }
    
    const onlineSwitch = document.getElementById('onlineSwitch');
    if (!onlineSwitch) return;

    onlineSwitch.addEventListener('change', async function () {
        const checked = this.checked ? 1 : 0;
        this.disabled = true;

        const params = new URLSearchParams();
        params.append('csrf_token', '<?= h($_SESSION['csrf_token']); ?>');
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