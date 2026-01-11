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
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="d-flex flex-column min-vh-100 bg-light">
    <?php include 'components/navbar.php'; ?>

    <main class="flex-grow-1 py-4">
        <div class="container">
            
            <div class="bg-white p-4 p-md-5" 
                 style="border: 3px solid #f8e14b; border-radius: 28px; box-shadow: 0 8px 20px rgba(0,0,0,0.06);">
                
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                    <h3 class="fw-bold m-0">TRANSACTION HISTORY</h3>
                    
                    <div class="w-100 w-md-auto" style="max-width: 300px;">
                        <input id="search" type="text" 
                               class="form-control border-0" 
                               placeholder="🔍 Search orders..."
                               style="background-color: #f1f3f5; border-radius: 20px; padding: 10px 20px;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless table-hover" style="border-collapse: separate; border-spacing: 0 10px;">
                        <thead>
                            <tr>
                                <th style="background-color: #f8e14b; padding: 14px; border-top-left-radius: 12px; border-bottom-left-radius: 12px;">Date</th>
                                <th style="background-color: #f8e14b; padding: 14px;">Order ID</th>
                                <th style="background-color: #f8e14b; padding: 14px;">Service</th>
                                <th style="background-color: #f8e14b; padding: 14px;">Amount</th>
                                <th style="background-color: #f8e14b; padding: 14px;">Vehicle</th>
                                <th style="background-color: #f8e14b; padding: 14px; border-top-right-radius: 12px; border-bottom-right-radius: 12px;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tx-body">
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5 rounded-3 bg-white">
                                        No orders found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $t): ?>
                                    <tr style="box-shadow: 0 3px 8px rgba(0,0,0,0.05);">
                                        <td class="text-secondary" style="background-color: #fff; vertical-align: middle; padding: 16px; border-top-left-radius: 12px; border-bottom-left-radius: 12px;">
                                            <?= h(date('M d, Y', strtotime($t['date']))) ?>
                                        </td>
                                        <td class="fw-bold" style="background-color: #fff; vertical-align: middle; padding: 16px;">
                                            #<?= (int)$t['id'] ?>
                                        </td>
                                        <td style="background-color: #fff; vertical-align: middle; padding: 16px;">
                                            Logistics
                                        </td>
                                        <td class="fw-bold" style="background-color: #fff; vertical-align: middle; padding: 16px;">
                                            ₱ <?= h(formatMoney($t['amount'])) ?>
                                        </td>
                                        <td style="background-color: #fff; vertical-align: middle; padding: 16px;">
                                            <?= h(ucfirst($t['vehicle'])) ?>
                                        </td>
                                        <td class="fw-bold <?= getStatusColor($t['status']) ?>" 
                                            style="background-color: #fff; vertical-align: middle; padding: 16px; border-top-right-radius: 12px; border-bottom-right-radius: 12px;">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

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