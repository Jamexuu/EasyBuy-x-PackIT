<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . "/../api/classes/Database.php";

// Require login (same pattern used in tracking.php)
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

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
function formatMoney($n): string {
    return number_format((float)$n, 2);
}

/**
 * Build timeline for a delivered booking (reused/simplified)
 */
function buildTimeline(string $status, string $date): array {
    $steps = [
        'pending'    => ['title' => 'Booking created',     'desc' => 'We received your booking request.'],
        'accepted'   => ['title' => 'Driver accepted',     'desc' => 'A driver has accepted your booking.'],
        'picked_up'  => ['title' => 'Package picked up',   'desc' => 'Your package has been picked up.'],
        'in_transit' => ['title' => 'In transit',          'desc' => 'Your package is on the way.'],
        'delivered'  => ['title' => 'Delivered',           'desc' => 'Your package has been delivered.'],
    ];

    if ($status === 'cancelled') {
        return [
            ['date' => date('M d', strtotime($date)), 'title' => 'Booking created', 'desc' => '', 'active' => false],
            ['date' => date('M d', strtotime($date)), 'title' => 'Cancelled', 'desc' => '', 'active' => true],
        ];
    }

    $order = array_keys($steps);
    $currentIndex = array_search($status, $order, true);
    if ($currentIndex === false) $currentIndex = 0;

    $timeline = [];
    foreach ($order as $i => $key) {
        $timeline[] = [
            'date' => date('M d', strtotime($date)),
            'title' => $steps[$key]['title'],
            'desc' => $steps[$key]['desc'],
            'active' => $i <= $currentIndex,
        ];
    }
    return $timeline;
}

// If booking_id provided show detail of that delivered booking (receipt/timeline)
$detailId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$booking = null;
$transactions = [];

if ($detailId > 0) {
    $stmt = $db->executeQuery(
        "SELECT b.*, p.amount AS paid_amount, p.currency AS currency, p.status AS payment_status, u.first_name, u.last_name
         FROM bookings b
         LEFT JOIN payments p ON p.booking_id = b.id
         LEFT JOIN users u ON u.id = b.user_id
         WHERE b.id = ? AND b.user_id = ? AND b.tracking_status = 'delivered'
         LIMIT 1",
        [$detailId, $userId]
    );
    $row = $db->fetch($stmt);
    $row = empty($row) ? [] : $row[0];

    if (empty($row)) {
        // not found or not delivered — go back to list
        header('Location: transaction.php');
        exit;
    }
    // build detail view variables
    $booking = [
        'id' => (int)$row['id'],
        'vehicle' => $row['vehicle_type'] ?? '',
        'price' => (float)($row['total_amount'] ?? 0),
        'paid' => isset($row['paid_amount']) ? (float)$row['paid_amount'] : null,
        'currency' => $row['currency'] ?? 'PHP',
        'payment_status' => $row['payment_status'] ?? '',
        'pickup' => trim(($row['pickup_municipality'] ?? '') . ', ' . ($row['pickup_province'] ?? '')),
        'drop' => trim(($row['drop_municipality'] ?? '') . ', ' . ($row['drop_province'] ?? '')),
        'created_at' => $row['created_at'] ?? '',
        'updated_at' => $row['updated_at'] ?? $row['created_at'] ?? '',
        'timeline' => buildTimeline($row['tracking_status'] ?? 'delivered', $row['updated_at'] ?? $row['created_at']),
        'customer' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
    ];
} else {
    // List delivered bookings (transaction history)
    $stmt = $db->executeQuery(
        "SELECT b.id, b.created_at, b.updated_at, b.vehicle_type, b.total_amount, p.amount AS paid_amount, p.currency, p.status AS payment_status
         FROM bookings b
         LEFT JOIN payments p ON p.booking_id = b.id
         WHERE b.user_id = ? AND b.tracking_status = 'delivered'
         ORDER BY COALESCE(b.updated_at, b.created_at) DESC",
        [(string)$userId]
    );
    $rows = $db->fetch($stmt);
    foreach ($rows as $r) {
        $transactions[] = [
            'id' => (int)$r['id'],
            'date' => $r['updated_at'] ?? $r['created_at'],
            'vehicle' => $r['vehicle_type'] ?? '',
            'amount' => isset($r['paid_amount']) && $r['paid_amount'] !== null ? (float)$r['paid_amount'] : (float)($r['total_amount'] ?? 0),
            'currency' => $r['currency'] ?? 'PHP',
            'payment_status' => $r['payment_status'] ?? '',
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
        : root {
            --brand-yellow: #f8e14b;
            --bg-light:  #f8f9fa;
            --text-muted: #6c757d;
        }

        /* --- UPDATED BODY CSS --- */
        body {
            background-color: var(--bg-light);
            padding-bottom: 0;

            /* These lines force the footer to the bottom */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* --- NEW MAIN CSS --- */
        main {
            flex: 1; /* This makes the content grow to push footer down */
            width: 100%; /* Ensures Bootstrap container centers correctly */
            margin-bottom: 30px; /* specific spacing before footer */
        }

        .custom-header {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .custom-header img {
            height:  40px;
        }

        .transaction-container {
            background-color: #ffffff;
            border:  3px solid var(--brand-yellow);
            border-radius: 28px;
            padding: 40px;
            box-shadow:  0 8px 20px rgba(0,0,0,0.06);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-header h3 {
            margin: 0;
            font-weight: 700;
        }

        .search-bar {
            background-color: #f1f3f5;
            border: none;
            border-radius: 20px;
            padding: 8px 18px;
            width: 240px;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0 10px;
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
        }

        .table tbody td {
            padding: 14px;
            border: none;
        }

        .table tbody tr:hover {
            background-color: #fffdf0;
        }

        .status-completed {
            color: #198754;
            font-weight: 600;
        }

        /* Timeline styles reused from tracking */
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
            background-color: #203a43;
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
                    <h3 class="mb-0">Transaction Receipt</h3>
                    <small class="text-muted">Booking #<?= (int)$booking['id'] ?> — <?= h($booking['customer']) ?></small>
                </div>
                <div>
                    <a href="transaction.php" class="btn btn-secondary">Back to History</a>
                </div>
            </div>

            <div class="mb-3">
                <h4>₱ <?= h(formatMoney($booking['price'])) ?></h4>
                <p class="mb-1"><strong>Vehicle:</strong> <?= h($booking['vehicle']) ?></p>
                <p class="mb-1"><strong>Pickup:</strong> <?= h($booking['pickup']) ?></p>
                <p class="mb-1"><strong>Drop:</strong> <?= h($booking['drop']) ?></p>
                <p class="mb-1"><strong>Payment:</strong> <?= h($booking['payment_status'] ?? '') ?> <?= isset($booking['paid']) ? ' - ₱' . h(formatMoney($booking['paid'])) : '' ?></p>
                <p class="text-muted small">Delivered on: <?= h(date('M d, Y H:i', strtotime($booking['updated_at']))) ?></p>
            </div>

            <hr>

            <h5 class="mb-3">Timeline</h5>
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
                <input id="search" type="text" class="search-bar form-control" placeholder="🔍 Search (booking id / vehicle)">
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Amount</th>
                            <th>Vehicle</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tx-body">
                        <?php if (empty($transactions)): ?>
                            <tr><td colspan="7" class="text-center text-muted">No delivered orders yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $t): ?>
                                <tr>
                                    <td><?= h(date('M d, Y', strtotime($t['date']))) ?></td>
                                    <td>#<?= (int)$t['id'] ?></td>
                                    <td>Delivery</td>
                                    <td>₱ <?= h(formatMoney($t['amount'])) ?></td>
                                    <td><?= h($t['vehicle']) ?></td>
                                    <td><span class="status-completed"><?= h(ucfirst($t['payment_status'] ?: 'completed')) ?></span></td>
                                    <td class="text-end">
                                        <a href="transaction.php?booking_id=<?= (int)$t['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
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