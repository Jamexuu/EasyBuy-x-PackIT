<?php
require_once __DIR__ . '/helpers.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Under Maintenance - PackIT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style> .notice-card{max-width:760px;margin:60px auto;} .notice-hero{font-size:3.5rem;} </style>
</head>
<body>

<?php
$navbarPath = __DIR__ . '/navbar.php';
if (file_exists($navbarPath)) include $navbarPath;
?>

<main class="container notice-card">
  <div class="card shadow-sm rounded-4 p-4">
    <div class="d-flex align-items-start gap-3">
      <div class="bg-light rounded-3 p-3 d-flex align-items-center justify-content-center" style="min-width:96px;">
        <i class="bi bi-tools text-info notice-hero"></i>
      </div>
      <div class="flex-grow-1">
        <h2 class="fw-bold mb-2">System Under Maintenance</h2>
        <p class="mb-3 text-muted">This feature is temporarily under maintenance. We are working on improvements and will restore it shortly. Please try again later.</p>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-secondary" onclick="history.back();">Head back</button>
          <a href="<?= htmlspecialchars(u('index.php')) ?>" class="btn btn-primary">Go to Home</a>
        </div>
      </div>
    </div>
  </div>
</main>

<?php
$footerPath = __DIR__ . '/footer.php';
if (file_exists($footerPath)) include $footerPath;
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>