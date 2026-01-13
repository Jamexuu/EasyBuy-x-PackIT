<?php
require_once __DIR__ . '/helpers.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Coming Soon - PackIT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style> .notice-card{max-width:760px;margin:60px auto;} .notice-hero{font-size:3.5rem;} </style>
</head>
<body class="d-flex flex-column min-vh-100">

<?php
// We use the shared navbar if it works, otherwise you might need to fix paths there too.
$navbarPath = __DIR__ . '/navbar.php';
if (file_exists($navbarPath)) include $navbarPath;
?>

<main class="container notice-card flex-grow-1">
  <div class="card shadow-sm rounded-4 p-4">
    <div class="d-flex align-items-start gap-3">
      <div class="bg-light rounded-3 p-3 d-flex align-items-center justify-content-center" style="min-width:96px;">
        <i class="bi bi-clock-history text-warning notice-hero"></i>
      </div>
      <div class="flex-grow-1">
        <h2 class="fw-bold mb-2">Coming Soon</h2>
        <p class="mb-3 text-muted">This feature is coming soon. We're putting the finishing touches on it and it will be available shortly. Thanks for your patience!</p>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-secondary" onclick="history.back();">Head back</button>
          <a href="../../index.php" class="btn btn-primary">Go to Home</a>
        </div>
      </div>
    </div>
  </div>
</main>

<footer class="pt-5 pb-4 text-dark mt-auto" style="background-color: #f8e15b;">
  <div class="container text-center text-md-start">
    <div class="row gy-4 justify-content-between">
      <div class="col-12 col-lg-4 d-flex align-items-center justify-content-center justify-content-md-start">
        <a href="../../index.php" class="d-inline-block">
          <img src="../../assets/LOGO.svg" alt="PackIT logo" class="img-fluid" style="max-height:70px;width:auto;">
        </a>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <h6 class="text-uppercase fw-bold mb-3">Services</h6>
        <div class="d-flex flex-column gap-2">
          <a href="comingSoon.php" class="text-dark text-decoration-none fw-medium">Express Delivery</a>
          <a href="underMaintenance.php" class="text-dark text-decoration-none fw-medium">Tracking</a>
          <a href="unavailable.php" class="text-dark text-decoration-none fw-medium">About Us</a>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <h6 class="text-uppercase fw-bold mb-3">Contact</h6>
        <p class="mb-2"><i class="bi bi-geo-alt-fill me-2"></i> Tanauan, Batangas, PH</p>
        <p class="mb-2"><i class="bi bi-envelope-at-fill me-2"></i> packit.notification@gmail.com</p>

        <div class="d-flex gap-2 mt-3 justify-content-center justify-content-md-start">
          <a href="#" class="btn btn-light rounded-circle border-0 bg-white bg-opacity-50" style="width:40px;height:40px;" title="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" class="btn btn-light rounded-circle border-0 bg-white bg-opacity-50" style="width:40px;height:40px;" title="X"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="btn btn-light rounded-circle border-0 bg-white bg-opacity-50" style="width:40px;height:40px;" title="Instagram"><i class="bi bi-instagram"></i></a>
        </div>
      </div>
    </div>

    <hr class="my-4 opacity-25 border-dark">

    <div class="row">
      <div class="col-12 text-center">
        <p class="mb-0 small fw-bold text-uppercase">© 2026 PackIT Logistics - Breaking Records Daily</p>
      </div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>