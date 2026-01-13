<?php
// frontend/components/driverFooter.php
// Driver-specific footer (components) — uses shared helpers.php to avoid redeclaring u()
// Links in Support point to the driver unavailable notice: driver/unavailable.php
// Include with: include __DIR__ . '/driverFooter.php';
require_once __DIR__ . '/helpers.php';
?>
<style>
  :root{
    --brand-yellow: #f8e15b;
    --brand-dark: #1e1e1e;
  }

  footer.packit-footer{
    background: var(--brand-yellow);
    color: var(--brand-dark);
    padding-top: 0; /* curve SVG sits on top */
  }

  .footer-curve{
    display:block;
    width:100%;
    line-height:0;
    transform: translateY(1px); /* remove tiny gap in some browsers */
  }
  .footer-curve svg{
    display:block;
    width:100%;
    height:90px;
  }

  .packit-logo{
    max-height: 80px;
    width:auto;
    object-fit:contain;
  }

  .packit-footer a{ color: var(--brand-dark); }
</style>

<footer class="packit-footer mt-4" role="contentinfo" aria-label="PackIT footer">
  <div class="footer-curve" aria-hidden="true">
    <svg viewBox="0 0 1200 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
      <path d="M0,0 C300,120 900,0 1200,120 L1200,0 L0,0 Z" fill="#f8e15b"></path>
    </svg>
  </div>

  <div class="container py-4">
    <div class="row gy-4 align-items-start">
      <div class="col-12 col-md-6 col-lg-4 d-flex align-items-center justify-content-center justify-content-md-start">
        <a href="<?= htmlspecialchars(u('driver.php')) ?>" aria-label="Driver dashboard">
          <img src="<?= htmlspecialchars(u('assets/LOGO.svg')) ?>" alt="PackIT logo" class="packit-logo">
        </a>
      </div>

      <div class="col-6 col-md-3 col-lg-2">
        <h6 class="text-uppercase fw-bold mb-3">Support</h6>
        <nav class="d-flex flex-column gap-2" aria-label="Support links">
          <!-- All support links point to the driver-specific unavailable notice page -->
          <a href="<?= htmlspecialchars(u('driver/unavailable.php')) ?>" class="text-decoration-none" role="link" aria-label="Driver Help Center (Unavailable)">Driver Help Center</a>
          <a href="<?= htmlspecialchars(u('driver/unavailable.php')) ?>" class="text-decoration-none" role="link" aria-label="Driver Guidelines (Unavailable)">Driver Guidelines</a>
          <a href="<?= htmlspecialchars(u('driver/unavailable.php')) ?>" class="text-decoration-none" role="link" aria-label="Safety Policy (Unavailable)">Safety Policy</a>
        </nav>
      </div>

      <div class="col-12 col-md-4 col-lg-3 ms-lg-auto">
        <h6 class="text-uppercase fw-bold mb-3">Contact</h6>
        <p class="mb-2"><i class="bi bi-geo-alt-fill me-2"></i> Tanauan, Batangas, PH</p>
        <p class="mb-0"><i class="bi bi-envelope-at-fill me-2"></i> help@packit.ph</p>
      </div>
    </div>

    <hr class="my-4 opacity-25 border-dark">

    <div class="text-center">
      <p class="mb-0 small fw-bold text-uppercase">© 2026 PackIT Logistics - Breaking Records Daily</p>
    </div>
  </div>
</footer>