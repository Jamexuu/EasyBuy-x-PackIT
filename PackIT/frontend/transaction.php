<?php
declare(strict_types=1);
session_start();

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

function getStatusColor($status) {
    return match (strtolower($status ?? '')) {
        'completed', 'delivered' => 'text-success',
        'pending' => 'text-warning',
        'accepted', 'in_transit', 'picked_up' => 'text-primary',
        'cancelled', 'failed' => 'text-danger',
        default => 'text-muted',
    };
}

// 2. Fetch Transactions
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --brand-yellow: #f8e14b;
            --bg-light:  #f8f9fa;
        }

        body {
            background-color: var(--bg-light);
        }

        /* --- Retained UI Styles --- */
        .transaction-container {
            background-color: #ffffff;
            border: 3px solid var(--brand-yellow);
            border-radius: 28px;
            /* Use Bootstrap padding classes for responsive padding, 
               but keep min-padding for the look */
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        }

        /* Search Bar Style */
        .custom-search-bar {
            background-color: #f1f3f5;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            transition: all 0.2s;
        }
        .custom-search-bar:focus {
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(248, 225, 75, 0.2); /* Brand yellow glow */
            outline: none;
        }

        /* Custom Table Styling to match original Look */
        .custom-table {
            border-collapse: separate; 
            border-spacing: 0 10px; /* Gap between rows */
        }

        .custom-table thead th {
            background-color: var(--brand-yellow);
            border: none;
            padding: 14px;
            font-weight: 600;
            white-space: nowrap; /* Prevent headers wrapping too aggressively */
        }

        /* First and Last header rounding */
        .custom-table thead th:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
        .custom-table thead th:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

        .custom-table tbody tr {
            background-color: #ffffff;
            box-shadow: 0 3px 8px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .custom-table tbody tr td {
            background-color: #fff; /* Needed for border-radius on tr to work visually in some browsers */
            border: none;
            padding: 16px;
            vertical-align: middle;
        }

        /* Rounding the row edges */
        .custom-table tbody tr td:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
        .custom-table tbody tr td:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

        .custom-table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.08);
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include 'components/navbar.php'; ?>

    <main class="flex-grow-1 py-4">
        <div class="container">
            
            <div class="transaction-container p-4 p-md-5">
                
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                    <h3 class="fw-bold m-0">TRANSACTION HISTORY</h3>
                    
                    <div class="w-100 w-md-auto" style="max-width: 300px;">
                        <input id="search" type="text" class="form-control custom-search-bar" placeholder="🔍 Search orders...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Order ID</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Vehicle</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="tx-body">
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5 rounded-3">
                                        No orders found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $t): ?>
                                    <tr>
                                        <td class="text-secondary"><?= h(date('M d, Y', strtotime($t['date']))) ?></td>
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

            </div>
        </div>
    </main>
    <?php include("../frontend/components/chat.php"); ?>
    <?php include 'components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Search Filter
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
</body>
</html>