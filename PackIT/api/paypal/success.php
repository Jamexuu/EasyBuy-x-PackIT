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
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PackIT - Success</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5 text-center">
    <div class="card shadow-sm border-0 rounded-4 p-5">
      <h1 class="text-success mb-3">Booking Confirmed!</h1>
      <p class="lead mb-4">Thank you for your payment. Your booking has been processed.</p>

      <?php if ($booking): ?>
        <div class="mx-auto text-start" style="max-width: 520px;">
          <div class="alert alert-success">
            <div class="fw-bold">Booking #<?= (int)$booking['id'] ?></div>
            <div class="small text-muted">Created: <?= h((string)$booking['created_at']) ?></div>
          </div>

          <ul class="list-group mb-4">
            <li class="list-group-item d-flex justify-content-between">
              <span>Vehicle</span>
              <strong><?= h((string)$booking['vehicle_type']) ?></strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Pickup</span>
              <strong class="text-end"><?= h((string)$booking['pickup_municipality']) ?>, <?= h((string)$booking['pickup_province']) ?></strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Drop-off</span>
              <strong class="text-end"><?= h((string)$booking['drop_municipality']) ?>, <?= h((string)$booking['drop_province']) ?></strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Total</span>
              <strong>₱<?= number_format((float)$booking['total_amount'], 2) ?></strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Payment</span>
              <strong><?= h((string)$booking['payment_status']) ?></strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Status</span>
              <strong><?= h((string)$booking['tracking_status']) ?></strong>
            </li>
          </ul>
        </div>
      <?php else: ?>
        <div class="alert alert-warning">
          Could not load booking details. (Missing or invalid booking_id)
        </div>
      <?php endif; ?>

      <div class="d-flex justify-content-center gap-2 flex-wrap">
        <a href="../../frontend/booking/package.php" class="btn btn-primary mt-2">Book Another</a>
        <a href="../../frontend/tracking.php" class="btn btn-outline-secondary mt-2">Go to Tracking</a>
      </div>
    </div>
  </div>
</body>
</html>