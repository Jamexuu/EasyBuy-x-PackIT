<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . "/../api/classes/Database.php";
include __DIR__ . '/components/autorefresh.php';


// Require login (because bookings.user_id is NOT NULL)
$userId = null;
if (!empty($_SESSION['user']['id'])) {
    $userId = (int)$_SESSION['user']['id'];
} elseif (!empty($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];
}

if (!$userId) {
    header("Location: login.php");
    exit;
}

$db = new Database();

// ----------------------------
// Helpers
// ----------------------------
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function formatMoney($n): string {
    return number_format((float)$n, 2);
}

/**
 * Map booking.tracking_status -> timeline items (with active flag)
 */
function buildTimeline(string $status, string $createdAt): array {
    // Order of statuses based on your ENUM:
    // pending, accepted, picked_up, in_transit, delivered, cancelled
    $steps = [
        'pending'    => ['title' => 'Booking created',     'desc' => 'We received your booking request.'],
        'accepted'   => ['title' => 'Driver accepted',     'desc' => 'A driver has accepted your booking.'],
        'picked_up'  => ['title' => 'Package picked up',   'desc' => 'Your package has been picked up.'],
        'in_transit' => ['title' => 'In transit',          'desc' => 'Your package is on the way.'],
        'delivered'  => ['title' => 'Delivered',           'desc' => 'Your package has been delivered.'],
    ];

    // Special handling for cancelled
    if ($status === 'cancelled') {
        return [
            [
                'date' => date('M d', strtotime($createdAt)),
                'title' => 'Booking created',
                'desc' => 'We received your booking request.',
                'active' => false,
            ],
            [
                'date' => date('M d', strtotime($createdAt)),
                'title' => 'Cancelled',
                'desc' => 'This booking has been cancelled.',
                'active' => true,
            ],
        ];
    }

    $order = array_keys($steps);
    $currentIndex = array_search($status, $order, true);
    if ($currentIndex === false) $currentIndex = 0;

    $timeline = [];
    foreach ($order as $i => $key) {
        $timeline[] = [
            'date' => date('M d', strtotime($createdAt)),
            'title' => $steps[$key]['title'],
            'desc' => $steps[$key]['desc'],
            'active' => $i <= $currentIndex,
        ];
    }
    return $timeline;
}

// ----------------------------
// Fetch list of bookings for user
// NOTE: Delivered bookings are excluded from tracking view and will appear in Transaction (history).
// ----------------------------
$stmt = $db->executeQuery(
    "SELECT id, user_id, driver_id,
            pickup_municipality, pickup_province,
            drop_municipality, drop_province,
            vehicle_type, total_amount, tracking_status, payment_status, created_at
     FROM bookings
     WHERE user_id = ? AND tracking_status != 'delivered'
     ORDER BY id DESC",
    [(string)$userId]
);
$rows = $db->fetch($stmt);

// Normalize into $orders array expected by your UI
$orders = [];
foreach ($rows as $r) {
    $id = (int)($r['id'] ?? 0);
    if ($id <= 0) continue;

    $createdAt = (string)($r['created_at'] ?? date('Y-m-d H:i:s'));
    $status = (string)($r['tracking_status'] ?? 'pending');

    $orders[$id] = [
        'id' => $id,
        'parcel_name' => (string)($r['vehicle_type'] ?? 'Delivery'),
        'items_count' => 1, // You don’t have items table yet; keep as 1 for now
        'price' => (float)($r['total_amount'] ?? 0),
        'status' => strtoupper(str_replace('_', ' ', $status)),
        'est_delivery' => date('M d', strtotime($createdAt . ' +1 day')), // placeholder estimate
        'pickup' => trim((string)($r['pickup_municipality'] ?? '') . ', ' . (string)($r['pickup_province'] ?? '')),
        'drop' => trim((string)($r['drop_municipality'] ?? '') . ', ' . (string)($r['drop_province'] ?? '')),
        'timeline' => buildTimeline($status, $createdAt),
        'created_at' => $createdAt,
        'tracking_status' => $status,
    ];
}

// ----------------------------
// View selection: list or detail
// If user requests a track_id that is delivered, redirect them to transactions (history) instead.
// ----------------------------
$view = 'list';
$activeOrder = null;

$trackId = isset($_GET['track_id']) ? (int)$_GET['track_id'] : 0;
if ($trackId > 0) {
    if (isset($orders[$trackId])) {
        $view = 'detail';
        $activeOrder = $orders[$trackId];
    } else {
        // Possibly the booking was delivered (and thus excluded). Redirect to transactions page.
        header('Location: transaction.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - PackIT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        :root {
            --gradient-color: linear-gradient(90deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            --secondary-teal: #203a43;
            --card-border: #203a43;
        }

        body {
            background-color: #f4f6f8;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-content {
            flex: 1;
            padding: 3rem 1rem;
        }

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

        .status-badge-green {
            background-color: #c9f29d;
            color: #2c5206;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
        }

        .empty-state-container {
            text-align: center;
            padding: 4rem 1rem;
            color: #6c757d;
        }
        .empty-state-img {
            width: 150px;
            margin-bottom: 1.5rem;
            opacity: 0.8;
        }

        .timeline {
            position: relative;
            padding-left: 3.5rem;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 24px; top: 8px; bottom: 0; width: 2px;
            background-color: #e0e0e0;
        }
        .timeline-item { position: relative; margin-bottom: 2.5rem; }
        .timeline-dot {
            position: absolute; left: -2.05rem; top: 6px;
            width: 14px; height: 14px;
            border-radius: 50%; background-color: #e0e0e0; border: 2px solid #fff; z-index: 1;
        }
        .timeline-item.active .timeline-dot {
            background-color: var(--secondary-teal);
            transform: scale(1.3);
        }
        .timeline-date {
            position: absolute; left: -7rem; width: 4.5rem; text-align: right;
            font-size: 0.85rem; color: #6c757d; font-weight: 700; text-transform: uppercase; top: 4px;
        }
        @media (max-width: 768px) {
            .timeline { padding-left: 1.5rem; border-left: 2px solid #e0e0e0; }
            .timeline::before { display: none; }
            .timeline-item { margin-bottom: 2rem; padding-left: 1rem; }
            .timeline-dot { left: -1.55rem; top: 5px; }
            .timeline-date { position: relative; left: 0; text-align: left; display: block; width: 100%; margin-bottom: 5px; }
        }
    </style>
</head>
<body>

<?php include __DIR__ . "/components/navbar.php"; ?>

<div class="main-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="main-container p-4 p-md-5">

                    <?php if (empty($orders)): ?>
                        <div class="empty-state-container">
                            <img src="../assets/box.png" alt="No Orders" class="empty-state-img">
                            <h4 class="fw-bold text-dark">No Active Orders</h4>
                            <p class="mb-4">It looks like you haven't booked any active deliveries yet. Delivered orders move to your Transaction History.</p>
                            <a href="booking/package.php" class="btn btn-warning fw-bold px-4 py-2 shadow-sm text-uppercase">
                                Book Now
                            </a>
                        </div>

                    <?php else: ?>

                        <?php if ($view === 'list'): ?>

                            <h4 class="fw-bold mb-4" style="color: var(--secondary-teal);">
                                <span class="material-symbols-outlined align-middle me-2">list_alt</span>
                                My Orders
                            </h4>

                            <?php foreach ($orders as $order): ?>
                                <div class="order-card-item p-4 mb-4">
                                    <div class="row align-items-center">

                                        <div class="col-md-2 text-center mb-3 mb-md-0">
                                            <div class="bg-white rounded p-3 d-inline-block shadow-sm">
                                                <img src="../assets/box.png" alt="Package" style="width: 40px; height: 40px;">
                                            </div>
                                        </div>

                                        <div class="col-md-7 mb-3 mb-md-0">
                                            <div class="mb-2">
                                                <span class="status-badge-green">
                                                    EXPECTED DELIVERY <?= h((string)$order['est_delivery']) ?>
                                                </span>
                                            </div>
                                            <h6 class="fw-bold mb-0 text-dark"><?= h((string)$order['parcel_name']) ?></h6>
                                            <small class="text-muted">BOOKING ID: <?= (int)$order['id'] ?></small>
                                            <div class="small text-muted mt-1">
                                                <?= h((string)$order['pickup']) ?> → <?= h((string)$order['drop']) ?>
                                            </div>
                                        </div>

                                        <div class="col-md-3 text-md-end text-start">
                                            <span class="fw-bold text-warning small text-uppercase" style="letter-spacing: 1px;">
                                                <?= h((string)$order['status']) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <hr class="my-3 text-muted opacity-25">

                                    <div class="row align-items-center">
                                        <div class="col-md-6 text-muted small">
                                            TOTAL ITEM: <?= (int)$order['items_count'] ?>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <span class="me-3 small fw-bold">
                                                TOTAL PAYMENT: ₱ <?= h(formatMoney($order['price'])) ?>
                                            </span>
                                            <a href="tracking.php?track_id=<?= (int)$order['id'] ?>" class="btn btn-warning fw-bold px-4 shadow-sm">
                                                TRACK
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <div class="mb-4">
                                <a href="tracking.php" class="text-decoration-none text-muted small fw-bold">
                                    <i class="bi bi-arrow-left me-1"></i> BACK TO ORDERS
                                </a>
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 gap-2">
                                <span class="status-badge-green fs-6 px-4 py-2">
                                    EXPECTED DELIVERY: <?= h((string)$activeOrder['est_delivery']) ?>
                                </span>
                                <span class="fw-bold text-warning text-uppercase">
                                    <?= h((string)$activeOrder['status']) ?>
                                </span>
                            </div>

                            <div class="mb-5 border-bottom pb-4">
                                <h2 class="fw-bold mb-1" style="color: var(--secondary-teal);">
                                    ₱ <?= h(formatMoney($activeOrder['price'])) ?>
                                </h2>
                                <p class="mb-1 fw-bold text-secondary fs-5"><?= h((string)$activeOrder['parcel_name']) ?></p>
                                <small class="text-muted">BOOKING ID: <?= (int)$activeOrder['id'] ?></small>
                                <div class="small text-muted mt-2">
                                    <div><strong>Pickup:</strong> <?= h((string)$activeOrder['pickup']) ?></div>
                                    <div><strong>Drop-off:</strong> <?= h((string)$activeOrder['drop']) ?></div>
                                </div>
                            </div>

                            <div class="timeline">
                                <?php foreach ($activeOrder['timeline'] as $log): ?>
                                    <div class="timeline-item <?= !empty($log['active']) ? 'active' : '' ?>">
                                        <span class="timeline-date"><?= h((string)$log['date']) ?></span>
                                        <div class="timeline-dot"></div>
                                        <h6 class="fw-bold mb-1" style="color: #0f2027;"><?= h((string)$log['title']) ?></h6>
                                        <?php if (!empty($log['desc'])): ?>
                                            <small class="text-muted"><?= h((string)$log['desc']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php endif; ?>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/components/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>