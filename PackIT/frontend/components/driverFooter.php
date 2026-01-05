<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pack IT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root{
      --brand-yellow: #f8e15b;
      --brand-dark: #1e1e1e;
    }

    /* Make the page a column flex container so footer can stick to bottom */
    html, body {
      height: 100%;
    }
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      margin: 0;
      color: var(--brand-dark);
      background: #fff;
    }

    /* Main content grows to fill available space */
    main {
      flex: 1 0 auto;
    }

    /* Footer styling */
    footer.packit-footer {
      background: var(--brand-yellow);
      color: var(--brand-dark);
      padding-top: 0; /* curve SVG sits on top */
      flex-shrink: 0;
    }

    /* Decorative curve on top of the footer using an inline SVG for predictable rendering */
    .footer-curve {
      display: block;
      width: 100%;
      line-height: 0;
      transform: translateY(1px); /* remove tiny gap in some browsers */
    }
    .footer-curve svg {
      display: block;
      width: 100%;
      height: 90px;
    }

    /* Footer content spacing */
    .packit-footer .container {
      padding-top: 0rem;
      padding-bottom: 1.25rem;
    }

    /* Small tweaks for layout */
    .packit-footer h5, .packit-footer h6 {
      color: var(--brand-dark);
    }

    .packit-footer a {
      color: var(--brand-dark);
    }

    /* Make the logo fit nicely on small screens */
    .packit-logo {
      max-height: 80px;
      width: auto;
      object-fit: contain;
    }

    /* Footer bottom text small */
    .packit-footer .footer-bottom {
      padding-top: .5rem;
      padding-bottom: .5rem;
    }

    @media (max-width: 575.98px) {
      .packit-footer .col-md-3 { margin-bottom: .5rem; }
    }
  </style>
</head>
<body>

  <!-- Footer -->
  <footer class="packit-footer" role="contentinfo" aria-label="PackIT footer">
    <!-- Decorative curve -->
    <div class="footer-curve" aria-hidden="true">
      <!-- SVG curve (scales to width) -->
      <svg viewBox="0 0 1200 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
        <path d="M0,0 C300,120 900,0 1200,120 L1200,0 L0,0 Z" fill="#f8e15b"></path>
      </svg>
    </div>

 <div class="container text-center text-md-start">
        <div class="row">
            <div class="container text-center text-md-start">
      <div class="row gy-4">
        <div class="col-12 col-md-6 col-lg-4 d-flex align-items-center">
          <div>
            <img src="/EasyBuy-x-PackIT/PackIT/assets/LOGO.svg" alt="PackIT logo" class="packit-logo">
          </div>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
          <h6 class="text-uppercase fw-bold mb-3">Support</h6>
          <nav class="d-flex flex-column gap-2" aria-label="Support links">
            <a href="#!" class="text-decoration-none">Driver Help Center</a>
            <a href="#!" class="text-decoration-none">Driver Guidelines</a>
            <a href="#!" class="text-decoration-none">Safety Policy</a>
          </nav>
        </div>

        <!-- add more columns as needed -->
        <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                <h6 class="text-uppercase fw-bold mb-4">Contact</h6>
                <p class="mb-2"><i class="bi bi-geo-alt-fill me-3"></i> Tanauan, Batangas, PH</p>
                <p class="mb-2"><i class="bi bi-envelope-at-fill me-3"></i> help@packit.ph</p>
                <div class="d-flex gap-2 mt-4 justify-content-center justify-content-md-start">
      </div>
    </div>

    <hr class="my-4 opacity-25 border-dark">

    <div class="row">
        <div class="col-12 text-center footer-bottom">
          <p class="mb-0 small fw-bold text-uppercase">© 2026 PackIT Logistics - Breaking Records Daily</p>
        </div>
      </div>
  </footer>

  <!-- Bootstrap JS (optional) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>