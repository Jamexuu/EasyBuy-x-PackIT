<?php
declare(strict_types=1);
session_start();

// FIX: Correct path to Database.php
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

// 2. Fetch All Transactions (List View Only)
$transactions = [];

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
    </style>
</head>

<body>
    <?php include __DIR__ . "/components/navbar.php"; ?>

    <main class="container transaction-container">
        
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
                        <th class="rounded-end-3">Status</th>
                    </tr>
                </thead>
                <tbody id="tx-body">
                    <?php if (empty($transactions)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-5">No orders found.</td></tr>
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
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

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