<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PackIT - About Us</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Optional: Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">

  <style>
    :root{
      --brand-yellow: #f8e15b;
      --brand-dark: #111217;
      --muted: #6c6f73;
      --accent: #0d6efd;
      --container-max: 1100px;
    }

    html, body { height: 100%; }
    body {
      font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      color: var(--brand-dark);
      background: linear-gradient(180deg,#fff 0%, #fbfbfc 100%);
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
      overflow-x: hidden;
    }

    /* Page container sizing */
    .page-container {
      max-width: var(--container-max);
      margin: 0 auto;
      padding: 2.25rem 1rem;
    }

    /* HERO */
    .hero {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1.5rem;
      align-items: center;
      padding: 2.25rem 0;
    }
    @media (min-width: 992px) {
      .hero {
        grid-template-columns: 1fr 1fr;
      }
    }

    .hero-card {
      background: linear-gradient(180deg, rgba(248,225,91,0.12), rgba(13,110,253,0.02));
      border-radius: 18px;
      padding: 2rem;
      box-shadow: 0 6px 30px rgba(16,24,40,0.06);
      transition: transform .25s ease, box-shadow .25s ease;
    }
    .hero-card:hover { transform: translateY(-6px); box-shadow: 0 10px 40px rgba(16,24,40,0.08); }

    .brand-badge {
      display:inline-flex;
      gap:.5rem;
      align-items:center;
      background: rgba(0,0,0,0.03);
      padding: .35rem .65rem;
      border-radius:999px;
      font-weight:600;
      font-size:.85rem;
    }

    .hero-title {
      font-size: clamp(1.6rem, 2.2vw, 2.6rem);
      font-weight: 800;
      margin-top: .8rem;
      margin-bottom: .6rem;
      line-height: 1.05;
    }

    .hero-lead {
      color: var(--muted);
      font-size: 1.02rem;
      margin-bottom: 1.1rem;
    }

    .cta-row { gap: .75rem; }

    .visual {
      display:flex;
      align-items:center;
      justify-content:center;
      padding: 1rem;
    }
    .visual img {
      width: 100%;
      max-width: 520px;
      height: auto;
      border-radius: 14px;
      object-fit: cover;
    }

    /* Features / Why choose us */
    .features {
      display: grid;
      grid-template-columns: repeat(1, minmax(0,1fr));
      gap: .75rem;
      margin-top: 1.5rem;
    }
    @media (min-width: 768px) {
      .features { grid-template-columns: repeat(3, 1fr); }
    }

    .feature {
      background: #fff;
      border: 1px solid rgba(16,24,40,0.04);
      padding: 1rem;
      border-radius: 12px;
      display:flex;
      gap:.85rem;
      align-items:flex-start;
      transition: transform .18s ease;
    }
    .feature:hover { transform: translateY(-6px); }
    .feature .fi {
      width:48px; height:48px; display:inline-grid; place-items:center;
      background: linear-gradient(135deg,var(--brand-yellow), #ffd66a);
      color: var(--brand-dark); border-radius:10px; font-size:1.25rem;
    }
    .feature h6 { margin:0; font-size:1rem; font-weight:700; }
    .feature p { margin:0; color:var(--muted); font-size:.95rem; }

    /* Stats */
    .stats {
      display:flex;
      gap: 1rem;
      margin-top: 1.5rem;
      flex-wrap:wrap;
    }
    .stat {
      background: linear-gradient(180deg,#fff,#fff);
      padding: .95rem 1.1rem;
      border-radius: 10px;
      border: 1px solid rgba(16,24,40,0.04);
      min-width: 160px;
      text-align:center;
    }
    .stat h3 { margin:0; font-weight:800; font-size:1.35rem; color:var(--brand-dark); }
    .stat p { margin:0; color:var(--muted); font-size:.9rem; }

    /* SERVICES CAROUSEL */
    .services {
      margin-top: 2rem;
    }
    .carousel .carousel-item img {
      height: 420px;
      object-fit: cover;
      border-radius: 12px;
    }
    @media (max-width: 575.98px) {
      .carousel .carousel-item img { height: 220px; }
    }

    /* Footer-like CTA */
    .cta-strip {
      margin-top: 2rem;
      background: linear-gradient(90deg, rgba(248,225,91,0.12), rgba(13,110,253,0.03));
      border-radius: 12px;
      padding: 1.25rem;
      display:flex;
      gap:1rem;
      align-items:center;
      justify-content:space-between;
      flex-wrap:wrap;
    }

    /* small utilities */
    .muted { color: var(--muted); }
    .rounded-lg { border-radius: 12px; }
    .mb-xxl { margin-bottom: 2rem; }

    /* subtle entrance animation */
    .reveal { opacity: 0; transform: translateY(8px); transition: opacity .5s ease, transform .5s ease; }
    .reveal.visible { opacity: 1; transform: none; }
  </style>
</head>

<body>

  <?php $page = basename($_SERVER['PHP_SELF']); ?>
  <?php include("components/navbar.php"); ?>

  <main class="page-container">

    <section class="hero">
      <div class="hero-card reveal" aria-labelledby="about-hero-title">
        <span class="brand-badge" aria-hidden="true"><img src="../assets/LOGO.svg" alt="" width="18" height="18" style="object-fit:contain"> PackIT</span>

        <h1 id="about-hero-title" class="hero-title">Logistics made simple, fast, and affordable</h1>

        <p class="hero-lead">
          We’re a Filipino nationwide delivery partner dedicated to safe, reliable, and cost-efficient shipping across Luzon, Visayas, and Mindanao — from documents to larger freight.
        </p>

        <div class="features" aria-hidden="false">
          <div class="feature">
            <div class="fi"><i class="bi bi-clock-history" aria-hidden="true"></i></div>
            <div>
              <h6>Fast Turnarounds</h6>
              <p>Optimized routes and local teams ensure quick delivery times.</p>
            </div>
          </div>

          <div class="feature">
            <div class="fi"><i class="bi bi-shield-check" aria-hidden="true"></i></div>
            <div>
              <h6>Trusted & Secure</h6>
              <p>Every package is handled with care and tracked end-to-end.</p>
            </div>
          </div>

          <div class="feature">
            <div class="fi"><i class="bi bi-wallet2" aria-hidden="true"></i></div>
            <div>
              <h6>Transparent Pricing</h6>
              <p>Competitive, simple pricing with no hidden fees.</p>
            </div>
          </div>
        </div>

        <div class="stats">
          <div class="stat" aria-hidden="false">
            <h3>+125k</h3>
            <p>Deliveries completed</p>
          </div>
          <div class="stat">
            <h3>98%</h3>
            <p>On-time rate</p>
          </div>
          <div class="stat">
            <h3>24/7</h3>
            <p>Driver support</p>
          </div>
        </div>

        <div class="cta-strip" role="region" aria-label="Call to action">
          <a class="btn btn-warning btn-lg" href="tracking.php" role="button">Track Shipment</a>
        </div>
      </div>

      <div class="visual reveal" aria-hidden="false">
        <img src="../assets/gif1.gif" alt="PackIT delivery truck animation" loading="lazy" width="640" height="360" />
      </div>
    </section>

    <section class="mt-4">
      <div class="row gy-4 align-items-center">
        <div class="col-lg-6 reveal">
          <h2 class="h3 mb-3">About PackIT</h2>
          <p class="muted mb-3">
            PackIT started with a simple idea — make shipping accessible to everyone using technology and local expertise.
            From same-day courier needs to scheduled freight deliveries, our mission is to provide predictable outcomes and an exceptional customer experience.
          </p>

          <ul class="list-unstyled">
            <li class="mb-2"><strong>Extensive coverage:</strong> Luzon, Visayas & Mindanao.</li>
            <li class="mb-2"><strong>Real-time tracking:</strong> Updates at every step.</li>
            <li class="mb-2"><strong>Dedicated support:</strong> Reach us by chat, email, or phone.</li>
          </ul>
        </div>

        <div class="col-lg-6 reveal">
          <img src="../assets/gif2.gif" alt="Warehouse animation" loading="lazy" class="rounded-lg" style="width:100%; max-height:360px; object-fit:cover;">
        </div>
      </div>
    </section>

    <section class="services reveal" aria-label="Our Services">
      <h2 class="h3 mb-3">Our Services</h2>

      <div id="servicesCarousel"
           class="carousel slide"
           data-bs-ride="carousel"
           data-bs-interval="3500"
           data-bs-pause="false"
           aria-label="Services carousel">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <img src="../assets/carousel1.png" class="d-block w-100 rounded-lg" alt="Same-day delivery" loading="lazy">
          </div>

          <div class="carousel-item">
            <img src="../assets/carousel2.png" class="d-block w-100 rounded-lg" alt="Nationwide freight" loading="lazy">
          </div>

          <div class="carousel-item">
            <img src="../assets/carousel3.png" class="d-block w-100 rounded-lg" alt="Warehousing solutions" loading="lazy">
          </div>

          <div class="carousel-item">
            <img src="../assets/carousel4.png" class="d-block w-100 rounded-lg" alt="Ecommerce fulfillment" loading="lazy">
          </div>

          <div class="carousel-item">
            <img src="../assets/carousel5.png" class="d-block w-100 rounded-lg" alt="Custom logistics" loading="lazy">
          </div>
        </div>

        <!-- Controls are siblings of .carousel-inner -->
        <button class="carousel-control-prev" type="button" data-bs-target="#servicesCarousel" data-bs-slide="prev" aria-label="Previous service">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#servicesCarousel" data-bs-slide="next" aria-label="Next service">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>

        <div class="mt-3 d-flex justify-content-center">
          <div class="carousel-indicators">
            <button type="button" data-bs-target="#servicesCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#servicesCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#servicesCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#servicesCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
            <button type="button" data-bs-target="#servicesCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
          </div>
        </div>
      </div>
    </section>

    <section class="mt-4 reveal" aria-label="Trusted by">
      <h3 class="h5 mb-4 text-center">What makes us better?</h3>
      <div class="row g-3">
        <div class="col-6 col-md-3">
          <div class="p-3 rounded-lg border text-center bg-white">
            <img src="../assets/robot.gif" alt="GIF 1" class="img-fluid" loading="lazy">
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="p-3 rounded-lg border text-center bg-white">
            <img src="../assets/motor.gif" alt="GIF 2" class="img-fluid" loading="lazy">
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="p-3 rounded-lg border text-center bg-white">
            <img src="../assets/clock.gif" alt="GIF 3" class="img-fluid" loading="lazy">
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="p-3 rounded-lg border text-center bg-white">
            <img src="../assets/delivery.gif" alt="GIF 4" class="img-fluid" loading="lazy">
          </div>
        </div>
      </div>
    </section>

  </main>

  <?php include("components/footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    (function(){
      const observer = new IntersectionObserver((entries)=>{
        entries.forEach(e=>{
          if (e.isIntersecting) {
            e.target.classList.add('visible');
            observer.unobserve(e.target);
          }
        });
      }, {threshold: 0.12});
      document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    })();

    document.addEventListener('DOMContentLoaded', () => {
      const stat = document.querySelectorAll('.stat h3');
      stat.forEach((el, idx) => {
        const val = el.textContent.replace('+','').replace('%','');
        if(!isNaN(val)) {
          let start = 0;
          const to = parseInt(val,10);
          const dur = 900;
          const step = Math.max(1, Math.round(to / (dur / 20)));
          const timer = setInterval(() => {
            start += step;
            if (start >= to) {
              start = to;
              clearInterval(timer);
            }
            el.textContent = (idx===1) ? start + '%' : (idx===0 ? start + '+' : start + (idx===2 ? '' : ''));
          }, 20);
        }
      });
    });
  </script>
</body>

</html>