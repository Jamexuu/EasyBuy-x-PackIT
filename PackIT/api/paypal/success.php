<?php

declare(strict_types=1);
session_start();

require_once __DIR__ . "/../classes/Database.php";
// include sendMail helper (uses PHPMailer)
require_once __DIR__ . "/../gmail/sendMail.php";

$bookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

$booking = null;
$db = null;
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

function h(string $s): string
{
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// Try to fetch user email and send receipt email (non-blocking: errors are logged)
if ($booking && isset($booking['user_id']) && (int)$booking['user_id'] > 0) {
  try {
    // Fetch user email
    $userStmt = $db->executeQuery(
      "SELECT email, first_name, last_name FROM users WHERE id = ? LIMIT 1",
      [(string)(int)$booking['user_id']]
    );
    $userRows = $db->fetch($userStmt);
    $user = $userRows[0] ?? null;
    $recipientEmail = $user['email'] ?? '';

    if ($recipientEmail) {
      $fullname = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
      if ($fullname === '') {
        $fullname = 'Customer';
      }

      // Build simple HTML receipt (feel free to expand/format as needed)
      $emailHtml = "<div style=\"font-family:Arial,Helvetica,sans-serif;color:#222;line-height:1.4\">";
      $emailHtml .= "<h2 style=\"color:#198754\">PackIT Booking Receipt</h2>";
      $emailHtml .= "<p>Hi " . h($fullname) . ",</p>";
      $emailHtml .= "<p>Thank you — your booking has been confirmed. Below are the details:</p>";

      $emailHtml .= "<table style=\"width:100%;max-width:600px;border-collapse:collapse\">";
      $emailHtml .= "<tr><td style=\"padding:8px;border-top:1px solid #eee;\">Booking #</td><td style=\"padding:8px;border-top:1px solid #eee;\"> " . (int)$booking['id'] . "</td></tr>";
      $emailHtml .= "<tr><td style=\"padding:8px;\">Created</td><td style=\"padding:8px;\"> " . h((string)$booking['created_at']) . "</td></tr>";
      $emailHtml .= "<tr><td style=\"padding:8px;\">Vehicle</td><td style=\"padding:8px;\"> " . h((string)$booking['vehicle_type']) . "</td></tr>";
      $emailHtml .= "<tr><td style=\"padding:8px;\">Pickup</td><td style=\"padding:8px;\"> " . h((string)$booking['pickup_municipality']) . ', ' . h((string)$booking['pickup_province']) . "</td></tr>";
      $emailHtml .= "<tr><td style=\"padding:8px;\">Drop-off</td><td style=\"padding:8px;\"> " . h((string)$booking['drop_municipality']) . ', ' . h((string)$booking['drop_province']) . "</td></tr>";
      $emailHtml .= "<tr><td style=\"padding:8px;\">Total</td><td style=\"padding:8px;\"> ₱" . number_format((float)$booking['total_amount'], 2) . "</td></tr>";
      $emailHtml .= "<tr><td style=\"padding:8px;\">Payment status</td><td style=\"padding:8px;\"> " . h((string)$booking['payment_status']) . "</td></tr>";
      $emailHtml .= "<tr><td style=\"padding:8px;border-bottom:1px solid #eee;\">Tracking status</td><td style=\"padding:8px;border-bottom:1px solid #eee;\"> " . h((string)$booking['tracking_status']) . "</td></tr>";
      $emailHtml .= "</table>";

      $emailHtml .= "<p style=\"margin-top:16px\">You may view tracking here: <a href=\"" . h((string)('/frontend/tracking.php?booking_id=' . $bookingId)) . "\">View Booking</a></p>";
      $emailHtml .= "<p style=\"color:#6c757d;font-size:13px\">If you did not make this booking, please contact support immediately.</p>";
      $emailHtml .= "</div>";

      // Subject
      $subject = "PackIT Receipt — Booking #" . (int)$booking['id'];

      // sendMail may throw Exception; catch below
      sendMail($recipientEmail, $subject, $emailHtml);
      // optionally: you can set a flag in DB that receipt_sent = 1 if you have that column
    }
  } catch (Exception $e) {
    // Log error but don't break the success page
    $logMsg = sprintf("[%s] sendMail error for booking %d: %s\n", date('c'), $bookingId, $e->getMessage());
    // write to a local log file - adjust path/permissions as needed
    @file_put_contents(__DIR__ . '/sendmail_log.txt', $logMsg, FILE_APPEND);
    // Also write to PHP error log
    error_log($logMsg);
  }
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

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Optional: add your PackIT global stylesheet if you have one -->
  <!-- <link href="../../frontend/assets/css/style.css" rel="stylesheet"> -->

  <style>
    /* Lightweight "PackIT-like" polish without needing a separate CSS file */
    .packit-page {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      background: #f8f9fa;
    }

    .packit-main {
      flex: 1;
    }

    .success-card {
      border: 0;
      border-radius: 1rem;
    }

    .success-badge {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(25, 135, 84, 0.12);
      color: #198754;
      font-size: 28px;
      margin-bottom: 12px;
    }

    .kv {
      display: flex;
      justify-content: space-between;
      gap: 16px;
    }

    .kv .label {
      color: #6c757d;
    }

    .kv .value {
      font-weight: 600;
      text-align: right;
    }
  </style>
</head>

<body class="packit-page">

  <!-- Navbar -->
  <?php if (file_exists($navbarPath)) {
    include $navbarPath;
  } ?>

  <main class="packit-main">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-7 col-xl-6">

          <div class="card success-card shadow-sm">
            <div class="card-body p-4 p-md-5">

              <div class="text-center">
                <div class="success-badge">✓</div>
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
                  <div class="kv py-2 border-bottom">
                    <div class="label">Vehicle</div>
                    <div class="value"><?= h((string)$booking['vehicle_type']) ?></div>
                  </div>

                  <div class="kv py-2 border-bottom">
                    <div class="label">Pickup</div>
                    <div class="value">
                      <?= h((string)$booking['pickup_municipality']) ?>, <?= h((string)$booking['pickup_province']) ?>
                    </div>
                  </div>

                  <div class="kv py-2 border-bottom">
                    <div class="label">Drop-off</div>
                    <div class="value">
                      <?= h((string)$booking['drop_municipality']) ?>, <?= h((string)$booking['drop_province']) ?>
                    </div>
                  </div>

                  <div class="kv py-2 border-bottom">
                    <div class="label">Payment Status</div>
                    <div class="value"><?= h((string)$booking['payment_status']) ?></div>
                  </div>

                  <div class="kv py-2">
                    <div class="label">Tracking Status</div>
                    <div class="value"><?= h((string)$booking['tracking_status']) ?></div>
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

  <!-- Footer -->
  <?php if (file_exists($chatPath)) {
    include $chatPath;
  } ?>
  <?php if (file_exists($footerPath)) {
    include $footerPath;
  } ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>