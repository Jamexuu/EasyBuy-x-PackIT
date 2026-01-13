<?php
// driver/unavailable.php
// Driver-specific "Unavailable" notice page that uses frontend/components driver navbar + footer
// Place at: driver/unavailable.php

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Load shared URL helper (defines u())
require_once __DIR__ . '/../frontend/components/helpers.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Unavailable — Driver | PackIT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body { background: #fff; }
    .notice-card { max-width: 760px; margin: 60px auto; }
    .notice-hero { font-size: 3.5rem; line-height: 1; }
    .driver-cta { margin-top: 1rem; }
  </style>
</head>
<body>

<?php
// Include the driver navbar from components
$navbarPath = __DIR__ . '/../frontend/components/driverNavbar.php';
if (file_exists($navbarPath)) include $navbarPath;
?>

<main class="container notice-card">
  <div class="card shadow-sm rounded-4 p-4">
    <div class="d-flex align-items-start gap-3">
      <div class="bg-light rounded-3 p-3 d-flex align-items-center justify-content-center" style="min-width:96px;">
        <i class="bi bi-slash-circle text-danger notice-hero" aria-hidden="true"></i>
      </div>
      <div class="flex-grow-1">
        <h2 class="fw-bold mb-2">Feature Not Available</h2>
        <p class="mb-3 text-muted">Sorry — this driver-specific feature or resource is not currently available. We're working to provide the best experience for drivers; please try other parts of the driver portal or come back later.</p>

        <div class="d-flex gap-2 driver-cta">
          <button class="btn btn-outline-secondary" onclick="history.back();">Head back</button>
          <a href="<?= htmlspecialchars(u('driver/driver.php')) ?>" class="btn btn-primary">Go to Driver Home</a>
        </div>
      </div>
    </div>
  </div>
</main>

<?php
// Include the driver footer from components
$footerPath = __DIR__ . '/../frontend/components/driverFooter.php';
if (file_exists($footerPath)) include $footerPath;
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>