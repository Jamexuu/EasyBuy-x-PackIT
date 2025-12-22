<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pack IT</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root {
      --brand-yellow: #f8e15b;
      --brand-dark: #111;
    }

    .bg-brand { background-color: var(--brand-yellow) !important; }
    
    body { font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }

    .hover-scale { transition: transform 0.2s ease-in-out; }
    .hover-scale:hover { transform: scale(1.15); }

    .footer-curve {
      height: 90px;
      background: var(--brand-yellow);
      clip-path: ellipse(85% 100% at 50% 100%);
    }
  </style>
</head>

<body id="top" class="min-vh-100 bg-white">

<?php $page = basename($_SERVER['PHP_SELF']); ?>
<?php include("frontend/components/navbar.php"); ?>

<main class="container my-5 py-lg-5">
  <div class="row align-items-center gy-5">
    <div class="col-lg-6">
      <h1 class="display-1 fw-black text-uppercase" style="font-weight: 900;">PACK IT</h1>
      <p class="lead fw-semibold mt-4 text-secondary">
        The gold standard of PH logistics. 🏆<br>
        Bridging gaps and breaking records, one delivery at a time.
      </p>
      <a href="mobile.php" class="btn bg-brand btn-lg fw-bold rounded-pill px-4 mt-3 hover-scale">Get Started</a>
    </div>

    <div class="col-lg-6 text-center">
      <img src="assets/mascot.png" class="img-fluid" alt="Mascot" style="max-height: 450px;">
    </div>
  </div>
</main>

<div class="floating-actions position-fixed top-50 end-0 translate-middle-y d-flex flex-column align-items-center gap-4 py-5 px-3 bg-brand rounded-start-pill shadow-lg" 
     style="z-index: 1050;">
     
  <a href="orders.php" class="d-flex flex-column align-items-center text-decoration-none text-dark fw-bold small hover-scale">
    <img src="assets/box.png" alt="Order" style="width: 35px; height: 35px;" class="mb-1">
    <span>Order</span>
  </a>

  <a href="tracking.php" class="d-flex flex-column align-items-center text-decoration-none text-dark fw-bold small hover-scale">
    <img src="assets/tracking.png" alt="Tracking" style="width: 35px; height: 35px;" class="mb-1">
    <span>Tracking</span>
  </a>
</div>

<?php include("frontend/components/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>