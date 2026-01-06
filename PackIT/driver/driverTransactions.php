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

/* Summary totals */
$totalTrips = count($transactions);
$totalRevenue = 0.0;
foreach ($transactions as $t) {
  $totalRevenue += (float)($t['total_amount'] ?? 0);
}
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

          <?php if (empty($transactions)): ?>
            <div class="text-center py-5 text-muted">
              <img src="../assets/box.png" alt="No transactions" style="width: 140px; opacity: .85" class="mb-3">
              <h5 class="fw-bold text-dark mb-1">No Transactions Yet</h5>
              <div>You don’t have any delivered bookings yet.</div>
            </div>
          <?php else: ?>
            <div class="list-scroll">
              <?php foreach ($transactions as $b): ?>
                <?php
                  $id = (int)($b['id'] ?? 0);
                  $collapseId = "txn_details_" . $id;

                  $pickupFull = fmtAddrRow($b, 'pickup');
                  $dropFull = fmtAddrRow($b, 'drop');

                  $customerName = trim(($b['user_first_name'] ?? '') . ' ' . ($b['user_last_name'] ?? ''));
                  $qty = (int)($b['package_quantity'] ?? 1);
                  $deliveredAt = (string)($b['updated_at'] ?? $b['created_at'] ?? '');

                  $pkgDesc = (string)($b['package_desc'] ?? '');
                  $pkgType = (string)($b['package_type'] ?? '');
                ?>

                <!-- Compact row -->
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

                  <!-- Collapsible details -->
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