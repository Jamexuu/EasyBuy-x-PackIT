<?php
session_start();
require_once __DIR__ . '/../api/classes/Database.php';

if (!isset($_SESSION['driver_id'])) {
  header("Location: index.php");
  exit;
}

$db = new Database();
$driverId = (int)$_SESSION['driver_id'];

/* Helpers */
function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money($n): string { return number_format((float)$n, 2); }

function fmtAddrRow(array $b, string $prefix): string {
  $parts = array_filter([
    $b[$prefix . '_house'] ?? null,
    $b[$prefix . '_barangay'] ?? null,
    $b[$prefix . '_municipality'] ?? null,
    $b[$prefix . '_province'] ?? null,
  ]);
  return implode(', ', $parts);
}

/**
 * Transactions = delivered bookings done by this driver.
 */
$stmt = $db->executeQuery(
  "SELECT b.*,
          u.first_name AS user_first_name,
          u.last_name AS user_last_name,
          u.contact_number AS user_contact_number,
          u.email AS user_email
   FROM bookings b
   JOIN users u ON b.user_id = u.id
   WHERE b.driver_id = ?
     AND b.tracking_status = 'delivered'
   ORDER BY b.updated_at DESC, b.created_at DESC",
  [$driverId]
);
$transactions = $db->fetch($stmt);

/* Fetch completed EasyBuy orders */
$easybuyTransactions = [];
try {
    $easybuyApiUrl = 'http://localhost/EasyBuy-x-PackIT/EasyBuy/api/getAllOrders.php';
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 5,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($easybuyApiUrl, false, $context);
    
    if ($response !== false) {
        $allEasybuyOrders = json_decode($response, true);
        
        if (is_array($allEasybuyOrders)) {
            // Filter only completed orders with status "order arrived"
            foreach ($allEasybuyOrders as $order) {
                $status = strtolower($order['status'] ?? '');
                if ($status === 'order arrived') {
                    $easybuyTransactions[] = $order;
                }
            }
        }
    }
} catch (Exception $e) {
    error_log('Failed to fetch EasyBuy transactions: ' . $e->getMessage());
}

/* Summary totals */
$totalTrips = count($transactions) + count($easybuyTransactions);
$totalRevenue = 0.0;
foreach ($transactions as $t) {
  $totalRevenue += (float)($t['total_amount'] ?? 0);
}
foreach ($easybuyTransactions as $t) {
  $totalRevenue += (float)($t['totalAmount'] ?? 0);
}

/* ----------------------------
   Combine & paginate transactions
   ---------------------------- */
// Normalize both lists into a single list with unified keys for sorting & paging
$allTransactions = [];

// Bookings
foreach ($transactions as $b) {
    $dt = $b['updated_at'] ?? $b['created_at'] ?? null;
    $timestamp = $dt ? strtotime($dt) : 0;
    $allTransactions[] = [
        'type' => 'booking',
        'id' => (int)($b['id'] ?? 0),
        'ts' => $timestamp,
        'created_at' => $dt,
        'data' => $b,
    ];
}

// EasyBuy orders
foreach ($easybuyTransactions as $order) {
    // orderDate or orderDateTime fields may differ; try multiple
    $dt = $order['orderDate'] ?? $order['order_date'] ?? null;
    $timestamp = $dt ? strtotime($dt) : 0;
    $allTransactions[] = [
        'type' => 'easybuy',
        'id' => $order['orderID'] ?? ($order['id'] ?? 0),
        'ts' => $timestamp,
        'created_at' => $dt,
        'data' => $order,
    ];
}

// Sort by timestamp desc (most recent first)
usort($allTransactions, function($a, $b) {
    return $b['ts'] <=> $a['ts'];
});

// Pagination settings
$perPage = 5;
$totalItems = count($allTransactions);
$totalPages = max(1, (int)ceil($totalItems / $perPage));
$page = max(1, (int)($_GET['page'] ?? 1));
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;
$pagedTransactions = array_slice($allTransactions, $offset, $perPage);

// For display counts
$showingFrom = $totalItems === 0 ? 0 : ($offset + 1);
$showingTo = min($totalItems, $offset + count($pagedTransactions));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Driver Transactions | PackIT</title>

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

    .stat-card {
      background: #f8f9fa;
      border: 1px solid #e9ecef;
      border-radius: 14px;
      padding: 14px 16px;
    }

    .status-badge-gray {
      font-weight: 800;
      font-size: 0.8rem;
      padding: 6px 16px;
      border-radius: 20px;
      display: inline-block;
      background-color: #e9ecef;
      color: #495057;
    }

    .txn-card {
      background-color: #f8f9fa;
      border-radius: 15px;
      border: 1px solid #e9ecef;
      padding: 1.1rem 1.25rem;
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
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .04em;
    }
    .kv .v { color: #212529; font-weight: 800; text-align: right; }

    .list-scroll { max-height: 70vh; overflow: auto; padding-right: .25rem; }

    .btn-link-lite {
      padding: 0;
      border: 0;
      background: transparent;
      color: #0d6efd;
      font-weight: 800;
      text-decoration: none;
    }
    .btn-link-lite:hover { text-decoration: underline; }

    /* Pagination controls */
    .txn-pagination {
      display:flex;
      justify-content:center;
      align-items:center;
      gap:8px;
      padding: 12px 0 18px;
    }
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
                <span class="material-symbols-outlined align-middle me-2">receipt_long</span>
                Transactions
              </h4>
              <div class="text-muted small">
                Your completed (delivered) bookings history.
              </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
              <a href="driver.php" class="btn btn-outline-dark fw-bold px-3 shadow-sm text-uppercase">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
              </a>
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="stat-card">
                <div class="small text-muted fw-bold text-uppercase">Total Delivered Bookings</div>
                <div class="fs-3 fw-bold"><?= (int)$totalTrips ?></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="stat-card">
                <div class="small text-muted fw-bold text-uppercase">Total Earnings (Gross)</div>
                <div class="fs-3 fw-bold text-warning">₱ <?= h(money($totalRevenue)) ?></div>
                <div class="small text-muted">Based on booking total_amount.</div>
              </div>
            </div>
          </div>

          <?php if ($totalItems === 0): ?>
            <div class="text-center py-5 text-muted">
              <img src="../assets/box.png" alt="No transactions" style="width: 140px; opacity: .85" class="mb-3">
              <h5 class="fw-bold text-dark mb-1">No Transactions Yet</h5>
              <div>You don’t have any delivered bookings yet.</div>
            </div>
          <?php else: ?>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="small text-muted">
                Showing <?= $showingFrom ?>–<?= $showingTo ?> of <?= $totalItems ?>
              </div>
              <div>
                <small class="text-muted">Page <?= $page ?> / <?= $totalPages ?></small>
              </div>
            </div>

            <div class="list-scroll">
              <?php foreach ($pagedTransactions as $entry): ?>
                <?php if ($entry['type'] === 'booking'): 
                  $b = $entry['data'];
                  $id = (int)($b['id'] ?? 0);
                  $collapseId = "txn_booking_" . $id;
                  $pickupFull = fmtAddrRow($b, 'pickup');
                  $dropFull = fmtAddrRow($b, 'drop');
                  $customerName = trim(($b['user_first_name'] ?? '') . ' ' . ($b['user_last_name'] ?? ''));
                  $qty = (int)($b['package_quantity'] ?? 1);
                  $deliveredAt = (string)($b['updated_at'] ?? $b['created_at'] ?? '');
                  $pkgDesc = (string)($b['package_desc'] ?? '');
                  $pkgType = (string)($b['package_type'] ?? '');
                ?>
                  <div class="txn-card mb-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                      <div>
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-1">
                          <span class="status-badge-gray">DELIVERED</span>
                          <span class="status-badge-gray">BOOKING #<?= $id ?></span>
                          <span class="status-badge-gray">VEHICLE: <?= h($b['vehicle_type'] ?? '—') ?></span>
                        </div>

                        <div class="fw-bold text-dark">
                          <?= h($customerName ?: '—') ?>
                          <?php if (!empty($b['user_contact_number'])): ?>
                            <span class="text-muted fw-normal">• <?= h($b['user_contact_number']) ?></span>
                          <?php endif; ?>
                        </div>

                        <div class="text-muted small">
                          <?= h($pickupFull ?: '—') ?> → <?= h($dropFull ?: '—') ?>
                        </div>
                      </div>

                      <div class="text-md-end">
                        <div class="fw-bold text-warning">₱ <?= h(money($b['total_amount'] ?? 0)) ?></div>
                        <div class="text-muted small">Delivered: <?= h($deliveredAt ?: '—') ?></div>
                        <button class="btn-link-lite mt-1" type="button"
                          data-bs-toggle="collapse"
                          data-bs-target="#<?= h($collapseId) ?>"
                          aria-expanded="false"
                          aria-controls="<?= h($collapseId) ?>">
                          View details
                        </button>
                      </div>
                    </div>

                    <div class="collapse mt-3" id="<?= h($collapseId) ?>">
                      <div class="bg-white border rounded-3 p-3">
                        <div class="row g-3">

                          <div class="col-12 col-md-6">
                            <div class="fw-bold mb-2"><i class="bi bi-geo-alt me-1"></i> Pickup</div>
                            <div class="text-muted small mb-2"><?= h($pickupFull ?: '—') ?></div>
                            <div class="kv"><div class="k">Pickup Contact</div><div class="v"><?= h(($b['pickup_contact_name'] ?? '—') . ' • ' . ($b['pickup_contact_number'] ?? '—')) ?></div></div>
                          </div>

                          <div class="col-12 col-md-6">
                            <div class="fw-bold mb-2"><i class="bi bi-flag me-1"></i> Drop-off</div>
                            <div class="text-muted small mb-2"><?= h($dropFull ?: '—') ?></div>
                            <div class="kv"><div class="k">Recipient</div><div class="v"><?= h(($b['drop_contact_name'] ?? '—') . ' • ' . ($b['drop_contact_number'] ?? '—')) ?></div></div>
                          </div>

                          <div class="col-12">
                            <div class="fw-bold mb-2"><i class="bi bi-box-seam me-1"></i> Package</div>
                            <div class="kv"><div class="k">Quantity</div><div class="v"><?= (int)$qty ?></div></div>
                            <div class="kv"><div class="k">Description</div><div class="v"><?= h($pkgDesc !== '' ? $pkgDesc : '—') ?></div></div>
                            <div class="kv"><div class="k">Type</div><div class="v"><?= h($pkgType !== '' ? $pkgType : '—') ?></div></div>
                          </div>

                          <div class="col-12">
                            <div class="fw-bold mb-2"><i class="bi bi-receipt me-1"></i> Fare & Payment</div>
                            <div class="kv"><div class="k">Base</div><div class="v">₱ <?= h(money($b['base_amount'] ?? 0)) ?></div></div>
                            <div class="kv"><div class="k">Distance</div><div class="v">₱ <?= h(money($b['distance_amount'] ?? 0)) ?></div></div>
                            <div class="kv"><div class="k">Total</div><div class="v text-warning">₱ <?= h(money($b['total_amount'] ?? 0)) ?></div></div>
                            <div class="kv"><div class="k">Payment</div><div class="v"><?= h(($b['payment_status'] ?? '—') . ' / ' . ($b['payment_method'] ?? '—')) ?></div></div>
                            <div class="kv"><div class="k">Created</div><div class="v"><?= h($b['created_at'] ?? '—') ?></div></div>
                          </div>

                        </div>

                        <div class="mt-3 text-end">
                          <button class="btn btn-sm btn-outline-secondary" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?= h($collapseId) ?>"
                            aria-expanded="true"
                            aria-controls="<?= h($collapseId) ?>">
                            Hide details
                          </button>
                        </div>
                      </div>
                    </div>

                  </div>
                <?php elseif ($entry['type'] === 'easybuy'):
                  $order = $entry['data'];
                  $orderId = $order['orderID'] ?? 0;
                  $collapseId = "easybuy_txn_" . $orderId;
                  $customerName = ($order['firstName'] ?? '') . ' ' . ($order['lastName'] ?? '');
                  $status = $order['status'] ?? 'order arrived';
                  $totalAmount = $order['totalAmount'] ?? 0;
                  $deliveredAt = $order['orderDate'] ?? '';
                  
                  $addr = $order['address'] ?? [];
                  $deliveryAddress = implode(', ', array_filter([
                      $addr['houseNumber'] ?? null,
                      $addr['street'] ?? null,
                      $addr['barangay'] ?? null,
                      $addr['city'] ?? null,
                      $addr['province'] ?? null,
                  ]));
                  
                  $itemCount = is_array($order['items'] ?? null) ? count($order['items']) : 0;
                ?>
                  <div class="txn-card mb-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                      <div>
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-1">
                          <span class="status-badge-gray">DELIVERED</span>
                          <span class="status-badge-gray">EASYBUY #<?= h($orderId) ?></span>
                          <span class="status-badge-gray">ITEMS: <?= h($itemCount) ?></span>
                        </div>

                        <div class="fw-bold text-dark">
                          <?= h($customerName ?: '—') ?>
                          <?php if (!empty($order['contactNumber'])): ?>
                            <span class="text-muted fw-normal">• <?= h($order['contactNumber']) ?></span>
                          <?php endif; ?>
                        </div>

                        <div class="text-muted small">
                          Delivered to: <?= h($deliveryAddress ?: '—') ?>
                        </div>
                      </div>

                      <div class="text-md-end">
                        <div class="fw-bold text-warning">₱ <?= h(money($totalAmount)) ?></div>
                        <div class="text-muted small">Delivered: <?= h($deliveredAt ?: '—') ?></div>
                        <button class="btn-link-lite mt-1" type="button"
                          data-bs-toggle="collapse"
                          data-bs-target="#<?= h($collapseId) ?>"
                          aria-expanded="false"
                          aria-controls="<?= h($collapseId) ?>">
                          View details
                        </button>
                      </div>
                    </div>

                    <div class="collapse mt-3" id="<?= h($collapseId) ?>">
                      <div class="bg-white border rounded-3 p-3">
                        <div class="row g-3">

                          <div class="col-12">
                            <div class="fw-bold mb-2"><i class="bi bi-bag-check me-1"></i> Order Items</div>
                            <?php if (!empty($order['items'])): ?>
                              <?php foreach ($order['items'] as $item): ?>
                                <div class="kv">
                                  <div class="k"><?= (int)($item['quantity'] ?? 1) ?>x <?= h($item['product_name'] ?? 'Product') ?></div>
                                  <div class="v">₱ <?= h(money($item['product_price'] ?? 0)) ?></div>
                                </div>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <div class="text-muted small">No items</div>
                            <?php endif; ?>
                          </div>

                          <div class="col-12">
                            <div class="fw-bold mb-2"><i class="bi bi-geo-alt me-1"></i> Delivery Address</div>
                            <div class="kv"><div class="k">Address</div><div class="v"><?= h($deliveryAddress ?: '—') ?></div></div>
                            <div class="kv"><div class="k">Contact</div><div class="v"><?= h($order['contactNumber'] ?? '—') ?></div></div>
                            <div class="kv"><div class="k">Email</div><div class="v"><?= h($order['userEmail'] ?? '—') ?></div></div>
                          </div>

                          <div class="col-12">
                            <div class="fw-bold mb-2"><i class="bi bi-receipt me-1"></i> Payment</div>
                            <div class="kv"><div class="k">Total</div><div class="v text-warning">₱ <?= h(money($totalAmount)) ?></div></div>
                            <div class="kv"><div class="k">Method</div><div class="v"><?= h($order['paymentMethod'] ?? '—') ?></div></div>
                            <div class="kv"><div class="k">Order Date</div><div class="v"><?= h($deliveredAt ?: '—') ?></div></div>
                          </div>

                        </div>

                        <div class="mt-3 text-end">
                          <button class="btn btn-sm btn-outline-secondary" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?= h($collapseId) ?>"
                            aria-expanded="true"
                            aria-controls="<?= h($collapseId) ?>">
                            Hide details
                          </button>
                        </div>
                      </div>
                    </div>

                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>

            <!-- Pagination controls -->
            <?php if ($totalPages > 1): ?>
              <div class="txn-pagination">
                <form method="get" style="display:inline;">
                  <input type="hidden" name="page" value="<?= max(1, $page - 1) ?>">
                  <button class="btn btn-outline-secondary btn-sm" type="submit" <?= $page <= 1 ? 'disabled' : '' ?>>
                    &larr; Back
                  </button>
                </form>

                <div class="small text-muted">Page <?= $page ?> of <?= $totalPages ?></div>

                <form method="get" style="display:inline;">
                  <input type="hidden" name="page" value="<?= min($totalPages, $page + 1) ?>">
                  <button class="btn btn-outline-secondary btn-sm" type="submit" <?= $page >= $totalPages ? 'disabled' : '' ?>>
                    Next &rarr;
                  </button>
                </form>
              </div>
            <?php endif; ?>

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