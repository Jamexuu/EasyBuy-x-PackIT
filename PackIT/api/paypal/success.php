<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . "/../classes/Database.php";

$bookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

$booking = null;
if ($bookingId > 0) {
    $db = new Database();
    $stmt = $db->executeQuery(
        "SELECT b.id, b.user_id,
                b.pickup_municipality, b.pickup_province,
                b.drop_municipality, b.drop_province,
                b.vehicle_type, b.total_amount,
                b.payment_status, b.tracking_status,
                b.created_at
         FROM bookings b
         WHERE b.id = ?
         LIMIT 1",
        [(string)$bookingId]
    );
    $rows = $db->fetch($stmt);
    $booking = $rows[0] ?? null;
}

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// Components (adjust if your folder structure differs)
$navbarPath = __DIR__ . '/../../frontend/components/navbar.php';
$footerPath = __DIR__ . '/../../frontend/components/footer.php';
$chatPath = __DIR__ . '/../../frontend/components/chat.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PackIT - Payment Success</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100 bg-light">

  <?php if (file_exists($navbarPath)) { include $navbarPath; } ?>

  <main class="flex-grow-1">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-7 col-xl-6">

          <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body p-4 p-md-5">

              <div class="text-center">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success mb-3" style="width: 56px; height: 56px; font-size: 28px;">
                  ✓
                </div>
                <h1 class="h3 text-success mb-2">Booking Confirmed</h1>
                <p class="text-muted mb-4">
                  Payment received. Your booking has been processed successfully.
                </p>
              </div>

              <?php if ($booking): ?>
                <div class="alert alert-success d-flex justify-content-between align-items-start flex-wrap gap-2">
                  <div>
                    <div class="fw-bold">Booking #<?= (int)$booking['id'] ?></div>
                    <div class="small text-muted">Created: <?= h((string)$booking['created_at']) ?></div>
                  </div>
                  <div class="text-end">
                    <div class="small text-muted">Total</div>
                    <div class="fw-bold">₱<?= number_format((float)$booking['total_amount'], 2) ?></div>
                  </div>
                </div>

                <div class="border rounded-3 p-3 p-md-4 mb-4 bg-white">
                  <div class="d-flex justify-content-between gap-3 py-2 border-bottom">
                    <div class="text-secondary">Vehicle</div>
                    <div class="fw-semibold text-end"><?= h((string)$booking['vehicle_type']) ?></div>
                  </div>

                  <div class="d-flex justify-content-between gap-3 py-2 border-bottom">
                    <div class="text-secondary">Pickup</div>
                    <div class="fw-semibold text-end">
                      <?= h((string)$booking['pickup_municipality']) ?>, <?= h((string)$booking['pickup_province']) ?>
                    </div>
                  </div>

                  <div class="d-flex justify-content-between gap-3 py-2 border-bottom">
                    <div class="text-secondary">Drop-off</div>
                    <div class="fw-semibold text-end">
                      <?= h((string)$booking['drop_municipality']) ?>, <?= h((string)$booking['drop_province']) ?>
                    </div>
                  </div>

                  <div class="d-flex justify-content-between gap-3 py-2 border-bottom">
                    <div class="text-secondary">Payment Status</div>
                    <div class="fw-semibold text-end"><?= h((string)$booking['payment_status']) ?></div>
                  </div>

                  <div class="d-flex justify-content-between gap-3 py-2">
                    <div class="text-secondary">Tracking Status</div>
                    <div class="fw-semibold text-end"><?= h((string)$booking['tracking_status']) ?></div>
                  </div>
                </div>

              <?php else: ?>
                <div class="alert alert-warning mb-4">
                  Could not load booking details. (Missing or invalid booking_id)
                </div>
              <?php endif; ?>

              <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="../../frontend/booking/package.php" class="btn btn-warning">
                  Book Another
                </a>
                <a href="../../frontend/tracking.php<?= $bookingId > 0 ? ('?booking_id=' . $bookingId) : '' ?>" class="btn btn-outline-secondary">
                  Go to Tracking
                </a>
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
  </main>

  <?php if (file_exists($chatPath)) { include $chatPath; } ?>
  <?php if (file_exists($footerPath)) { include $footerPath; } ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>