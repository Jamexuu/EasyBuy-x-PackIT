<?php
declare(strict_types=1);
session_start();

// FIX: Changed from "/../../" to "/../" because transaction.php is in 'frontend', not 'frontend/booking'
require_once __DIR__ . "/../api/classes/Database.php";

// 1. Authentication Check
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

// --- Helpers ---
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function formatMoney($n): string {
    return number_format((float)$n, 2);
}

// Helper to color-code statuses
function getStatusColor($status) {
    return match (strtolower($status ?? '')) {
        'completed', 'delivered' => 'text-success',
        'pending' => 'text-warning',
        'accepted', 'in_transit', 'picked_up' => 'text-primary',
        'cancelled', 'failed' => 'text-danger',
        default => 'text-muted',
    };
}

// 2. Build Timeline Logic
function buildTimeline(string $status, string $date): array {
    $steps = [
        'pending'    => ['title' => 'Booking Created',     'desc' => 'We received your booking request.'],
        'accepted'   => ['title' => 'Driver Accepted',     'desc' => 'A driver has accepted your booking.'],
        'picked_up'  => ['title' => 'Package Picked Up',   'desc' => 'Your package has been picked up.'],
        'in_transit' => ['title' => 'In Transit',          'desc' => 'Your package is on the way.'],
        'delivered'  => ['title' => 'Delivered',           'desc' => 'Your package has been delivered.'],
    ];

    if ($status === 'cancelled') {
        return [
            ['date' => date('M d', strtotime($date)), 'title' => 'Booking Created', 'desc' => '', 'active' => false],
            ['date' => date('M d', strtotime($date)), 'title' => 'Cancelled', 'desc' => 'This booking was cancelled.', 'active' => true],
        ];
    }

    $order = array_keys($steps);
    $currentIndex = array_search($status, $order, true);
    if ($currentIndex === false) $currentIndex = 0; // Default to first step if unknown

    $timeline = [];
    foreach ($order as $i => $key) {
        $timeline[] = [
            'date' => ($i <= $currentIndex) ? date('M d', strtotime($date)) : '', // Only show date for past/current steps
            'title' => $steps[$key]['title'],
            'desc' => $steps[$key]['desc'],
            'active' => $i <= $currentIndex,
        ];
    }
    return $timeline;
}

// 3. Handle Detail View vs List View
$detailId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$booking = null;
$transactions = [];

if ($detailId > 0) {
    // --- DETAIL VIEW QUERY ---
    $stmt = $db->executeQuery(
        "SELECT b.*, p.amount AS paid_amount, p.currency AS currency, p.status AS payment_status, u.first_name, u.last_name
         FROM bookings b
         LEFT JOIN payments p ON p.booking_id = b.id
         LEFT JOIN users u ON u.id = b.user_id
         WHERE b.id = ? AND b.user_id = ?
         LIMIT 1",
        [$detailId, $userId]
    );
    $row = $db->fetch($stmt);
    $row = empty($row) ? [] : $row[0];

    if (empty($row)) {
        header('Location: transaction.php');
        exit;
    }

    $booking = [
        'id' => (int)$row['id'],
        'vehicle' => $row['vehicle_type'] ?? '',
        'price' => (float)($row['total_amount'] ?? 0),
        'paid' => isset($row['paid_amount']) ? (float)$row['paid_amount'] : null,
        'currency' => $row['currency'] ?? 'PHP',
        'status' => $row['tracking_status'] ?? 'pending',
        'payment_status' => $row['payment_status'] ?? 'Unpaid',
        'pickup' => trim(($row['pickup_municipality'] ?? '') . ', ' . ($row['pickup_province'] ?? '')),
        'drop' => trim(($row['drop_municipality'] ?? '') . ', ' . ($row['drop_province'] ?? '')),
        'created_at' => $row['created_at'] ?? '',
        'updated_at' => $row['updated_at'] ?? $row['created_at'] ?? '',
        // Generate timeline
        'timeline' => buildTimeline($row['tracking_status'] ?? 'pending', $row['updated_at'] ?? $row['created_at']),
        'customer' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
    ];

} else {
    // --- LIST VIEW QUERY ---
    $stmt = $db->executeQuery(
        "SELECT b.id, b.created_at, b.updated_at, b.vehicle_type, b.total_amount, b.tracking_status,
                p.amount AS paid_amount, p.currency, p.status AS payment_status
         FROM bookings b
         LEFT JOIN payments p ON p.booking_id = b.id
         WHERE b.user_id = ?
         ORDER BY b.created_at DESC",
        [(string)$userId]
    );
    $rows = $db->fetch($stmt);
    
    foreach ($rows as $r) {
        $transactions[] = [
            'id' => (int)$r['id'],
            'date' => $r['created_at'],
            'vehicle' => $r['vehicle_type'] ?? '',
            'amount' => (float)($r['total_amount'] ?? 0),
            'currency' => $r['currency'] ?? 'PHP',
            'status' => $r['tracking_status'] ?? 'pending',
            'payment_status' => $r['payment_status'] ?? 'unpaid',
        ];
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaction History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --brand-yellow: #f8e14b;
            --bg-light:  #f8f9fa;
            --text-muted: #6c757d;
        }

        body {
            background-color: var(--bg-light);
            padding-bottom: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
            width: 100%;
            margin-bottom: 30px;
        }

        .transaction-container {
            background-color: #ffffff;
            border:  3px solid var(--brand-yellow);
            border-radius: 28px;
            padding: 40px;
            box-shadow:  0 8px 20px rgba(0,0,0,0.06);
            margin-top: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-bar {
            background-color: #f1f3f5;
            border: none;
            border-radius: 20px;
            padding: 8px 18px;
            width: 240px;
        }

        .table thead th {
            background-color:  var(--brand-yellow);
            border: none;
            padding: 14px;
            font-weight: 600;
        }

        .table tbody tr {
            background-color: #ffffff;
            box-shadow: 0 3px 8px rgba(0,0,0,0.05);
            border-radius: 12px;
            transition: transform 0.2s;
        }
        
        .table tbody tr:hover {
            transform: translateY(-2px);
            background-color: #fffdf0;
        }

        .table tbody td {
            padding: 14px;
            border: none;
            vertical-align: middle;
        }

        /* Timeline Styles */
        .timeline {
            position: relative;
            padding-left: 3.5rem;
            margin-top: 1rem;
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
            background-color: #f8e14b;
            border-color: #000;
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

    <main class="container transaction-container">
        
        <?php if ($detailId > 0 && $booking !== null): ?>
            <div class="page-header mb-4">
                <div>
                    <h3 class="mb-0">Booking Details</h3>
                    <small class="text-muted">Order #<?= (int)$booking['id'] ?></small>
                </div>
                <div>
                    <a href="transaction.php" class="btn btn-outline-dark rounded-pill px-4">Back to History</a>
                </div>
            </div>

            <div class="card border-0 bg-light p-3 mb-4 rounded-4">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="fw-bold mb-0">₱ <?= h(formatMoney($booking['price'])) ?></h2>
                        <span class="badge bg-warning text-dark mb-3"><?= h(strtoupper($booking['payment_status'])) ?></span>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="text-muted small">Vehicle</div>
                        <div class="fw-bold"><?= h(ucfirst($booking['vehicle'])) ?></div>
                    </div>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted">Pickup Location</small>
                        <p class="fw-medium mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= h($booking['pickup']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Drop-off Location</small>
                        <p class="fw-medium mb-0"><i class="bi bi-geo-alt-fill text-success me-1"></i> <?= h($booking['drop']) ?></p>
                    </div>
                </div>
            </div>

            <h5 class="mb-3">Delivery Progress</h5>
            <div class="timeline">
                <?php foreach ($booking['timeline'] as $log): ?>
                    <div class="timeline-item <?= !empty($log['active']) ? 'active' : '' ?>">
                        <span class="timeline-date"><?= h((string)$log['date']) ?></span>
                        <div class="timeline-dot"></div>
                        <h6 class="fw-bold mb-1"><?= h((string)$log['title']) ?></h6>
                        <?php if (!empty($log['desc'])): ?>
                            <small class="text-muted"><?= h((string)$log['desc']) ?></small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>

            <div class="page-header">
                <h3>TRANSACTION HISTORY</h3>
                <input id="search" type="text" class="search-bar form-control" placeholder="🔍 Search orders...">
            </div>

            <div class="table-responsive">
                <table class="table align-middle" style="border-collapse: separate; border-spacing: 0 10px;">
                    <thead>
                        <tr>
                            <th class="rounded-start-3">Date</th>
                            <th>Order ID</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Vehicle</th>
                            <th>Status</th>
                            <th class="rounded-end-3"></th>
                        </tr>
                    </thead>
                    <tbody id="tx-body">
                        <?php if (empty($transactions)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-5">No orders found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $t): ?>
                                <tr>
                                    <td><?= h(date('M d, Y', strtotime($t['date']))) ?></td>
                                    <td class="fw-bold">#<?= (int)$t['id'] ?></td>
                                    <td>Logistics</td>
                                    <td class="fw-bold">₱ <?= h(formatMoney($t['amount'])) ?></td>
                                    <td><?= h(ucfirst($t['vehicle'])) ?></td>
                                    <td class="fw-bold <?= getStatusColor($t['status']) ?>">
                                        <?= h(ucwords(str_replace('_', ' ', $t['status']))) ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="transaction.php?booking_id=<?= (int)$t['id'] ?>" class="btn btn-sm btn-light border rounded-pill px-3">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/components/footer.php'; ?>

    <script>
    // Simple frontend search filter
    document.getElementById('search')?.addEventListener('input', function (e) {
        const q = e.target.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#tx-body tr');
        rows.forEach(row => {
            if (!q) {
                row.style.display = '';
                return;
            }
            const text = row.textContent.toLowerCase();
            row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
        });
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>