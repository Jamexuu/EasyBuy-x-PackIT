<?php
// Admin footer component for PackIT admin area
// Place at: frontend/components/adminFooter.php
// Uses helpers.php to provide u() helper (require_once prevents redeclare)

require_once __DIR__ . '/helpers.php';
?>
<footer class="pt-5 pb-4 text-dark mt-auto" style="background-color: #f8e15b;">
    <div class="container text-center text-md-start">
        <div class="row gy-4 justify-content-between">
            
            <div class="col-12 col-md-6 col-lg-4 d-flex align-items-center justify-content-center justify-content-md-start">
                <div>
                    <a href="<?= htmlspecialchars(u('index.php')) ?>">
                        <img src="<?= htmlspecialchars(u('assets/LOGO.svg')) ?>" alt="PackIT logo" class="packit-logo" style="height: auto; max-width: 100%;">
                    </a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 text-center text-md-start">
                <h6 class="text-uppercase fw-bold mb-4">Contact</h6>
                <p class="mb-2"><i class="bi bi-geo-alt-fill me-3"></i> Tanauan, Batangas, PH</p>
                <p class="mb-2"><i class="bi bi-envelope-at-fill me-3"></i> help@packit.ph</p>
                
                <div class="d-flex gap-2 mt-4 justify-content-center justify-content-md-start">
                    <!-- Social icons -> Unavailable page -->
                    <a href="<?= htmlspecialchars(u('frontend/components/unavailable.php')) ?>" class="btn btn-light rounded-circle border-0 bg-white bg-opacity-50 d-inline-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;" title="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="<?= htmlspecialchars(u('frontend/components/unavailable.php')) ?>" class="btn btn-light rounded-circle border-0 bg-white bg-opacity-50 d-inline-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;" title="X / Twitter">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                    <a href="<?= htmlspecialchars(u('frontend/components/unavailable.php')) ?>" class="btn btn-light rounded-circle border-0 bg-white bg-opacity-50 d-inline-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;" title="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                </div>
            </div>
        </div>

        <hr class="my-4 opacity-25 border-dark">

        <div class="row">
            <div class="col-md-12 text-center">
                <p class="mb-0 small fw-bold text-uppercase">© 2025 PackIT Logistics - Breaking Records Daily</p>
            </div>
        </div>
    </div>
</footer>