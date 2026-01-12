<?php
// frontend/components/footer.php
require_once __DIR__ . '/helpers.php';
?>
<footer class="pt-5 pb-4 text-dark mt-auto" style="background-color: #f8e15b;">
  <div class="container text-center text-md-start">
    <div class="row gy-4 justify-content-between">
      <div class="col-12 col-lg-4 d-flex align-items-center justify-content-center justify-content-md-start">
        <a href="<?= htmlspecialchars(u('index.php')) ?>" class="d-inline-block">
          <img src="<?= htmlspecialchars(u('assets/LOGO.svg')) ?>" alt="PackIT logo" class="img-fluid" style="max-height:70px;width:auto;">
        </a>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <h6 class="text-uppercase fw-bold mb-3">Services</h6>
        <div class="d-flex flex-column gap-2">
          <a href="<?= htmlspecialchars(u('frontend/components/comingSoon.php')) ?>" class="text-dark text-decoration-none fw-medium">Express Delivery</a>
          <a href="<?= htmlspecialchars(u('frontend/components/underMaintenance.php')) ?>" class="text-dark text-decoration-none fw-medium">Tracking</a>
          <a href="<?= htmlspecialchars(u('frontend/components/unavailable.php')) ?>" class="text-dark text-decoration-none fw-medium">About Us</a>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <h6 class="text-uppercase fw-bold mb-3">Contact</h6>
        <p class="mb-2"><i class="bi bi-geo-alt-fill me-2"></i> Tanauan, Batangas, PH</p>
        <p class="mb-2"><i class="bi bi-envelope-at-fill me-2"></i> packit.notification@gmail.com</p>

        <div class="d-flex gap-2 mt-3 justify-content-center justify-content-md-start">
          <a href="<?= htmlspecialchars(u('frontend/components/unavailable.php')) ?>" class="btn btn-light rounded-circle border-0 bg-white bg-opacity-50" style="width:40px;height:40px;" title="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="<?= htmlspecialchars(u('frontend/components/unavailable.php')) ?>" class="btn btn-light rounded-circle border-0 bg-white bg-opacity-50" style="width:40px;height:40px;" title="X"><i class="bi bi-twitter-x"></i></a>
          <a href="<?= htmlspecialchars(u('frontend/components/unavailable.php')) ?>" class="btn btn-light rounded-circle border-0 bg-white bg-opacity-50" style="width:40px;height:40px;" title="Instagram"><i class="bi bi-instagram"></i></a>
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