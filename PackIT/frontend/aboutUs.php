<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PackIT - About Us</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet" />

  <style>
    :root {
      --brand-yellow: #f8e15b;
      --brand-dark: #111217;
      --muted: #6c6f73;
      --accent: #0d6efd;
      --container-max: 1100px;
      --gap: 1rem;
      --anim-ease: cubic-bezier(.2, .9, .2, 1);
      --card-border: rgba(16, 24, 40, 0.04);
    }

    html,
    body {
      height: 100%;
      margin: 0
    }

    body {
      font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      color: var(--brand-dark);
      background: transparent;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      overflow-x: hidden;
    }

    .page-container {
      max-width: var(--container-max);
      margin: 0 auto;
      padding: 2.25rem 1rem;
      box-sizing: border-box;
    }

    /* Hero */
    .hero {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1.5rem;
      align-items: center;
      padding: 2.25rem 0
    }

    @media(min-width:992px) {
      .hero {
        grid-template-columns: 1fr 1fr
      }
    }

    .hero-card {
      background: linear-gradient(180deg, rgba(248, 225, 91, 0.12), rgba(13, 110, 253, 0.02));
      border-radius: 18px;
      padding: 2rem;
      box-shadow: 0 6px 30px rgba(16, 24, 40, 0.06)
    }

    .brand-badge {
      display: inline-flex;
      gap: .5rem;
      align-items: center;
      background: rgba(0, 0, 0, 0.03);
      padding: .35rem .65rem;
      border-radius: 999px;
      font-weight: 600;
      font-size: .85rem
    }

    .hero-title {
      font-size: clamp(1.6rem, 2.2vw, 2.6rem);
      font-weight: 800;
      margin: .8rem 0 .6rem;
      line-height: 1.05
    }

    .hero-lead {
      color: var(--muted);
      font-size: 1.02rem;
      margin-bottom: 1.1rem
    }

    .visual {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem
    }

    .visual img {
      width: 100%;
      max-width: 520px;
      height: auto;
      border-radius: 14px;
      object-fit: cover
    }

    /* Features */
    .features {
      display: grid;
      grid-template-columns: 1fr;
      gap: .75rem;
      margin-top: 1.5rem
    }

    @media(min-width:768px) {
      .features {
        grid-template-columns: repeat(3, 1fr)
      }
    }

    .feature {
      background: #fff;
      border: 1px solid var(--card-border);
      padding: 1rem;
      border-radius: 12px;
      display: flex;
      gap: .85rem;
      align-items: flex-start
    }

    .feature .fi {
      width: 48px;
      height: 48px;
      display: inline-grid;
      place-items: center;
      background: linear-gradient(135deg, var(--brand-yellow), #ffd66a);
      color: var(--brand-dark);
      border-radius: 10px;
      font-size: 1.25rem
    }

    .feature h6 {
      margin: 0;
      font-size: 1rem;
      font-weight: 700
    }

    .feature p {
      margin: 0;
      color: var(--muted);
      font-size: .95rem
    }

    /* Stats */
    .stats {
      display: flex;
      gap: 1rem;
      margin-top: 1.5rem;
      flex-wrap: wrap
    }

    .stat {
      background: linear-gradient(180deg, #fff, #fff);
      padding: .95rem 1.1rem;
      border-radius: 10px;
      border: 1px solid var(--card-border);
      min-width: 160px;
      text-align: center
    }

    .stat h3 {
      margin: 0;
      font-weight: 800;
      font-size: 1.35rem;
      color: var(--brand-dark)
    }

    .stat p {
      margin: 0;
      color: var(--muted);
      font-size: .9rem
    }

    /* Carousel */
    .services {
      margin-top: 2rem
    }

    .carousel .carousel-item img {
      height: 420px;
      object-fit: cover;
      border-radius: 12px
    }

    @media(max-width:575.98px) {
      .carousel .carousel-item img {
        height: 220px
      }
    }

    .cta-strip {
      margin-top: 2rem;
      background: linear-gradient(90deg, rgba(248, 225, 91, 0.12), rgba(13, 110, 253, 0.03));
      border-radius: 12px;
      padding: 1.25rem;
      display: flex;
      gap: 1rem;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap
    }

    .muted {
      color: var(--muted)
    }

    .rounded-lg {
      border-radius: 12px
    }

    .mb-xxl {
      margin-bottom: 2rem
    }

    /* How we deliver */
    .how-we-deliver {
      margin-top: 2rem
    }

    .delivery-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: var(--gap);
      align-items: start;
      margin-top: 1rem
    }

    .delivery-card {
      background: #fff;
      border: 1px solid var(--card-border);
      border-radius: 12px;
      padding: clamp(.85rem, 1.2vw, 1.25rem);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      gap: .6rem;
      position: relative;
      text-align: center;
      min-height: 220px;
      box-sizing: border-box
    }

    .badge.top-right {
      position: absolute;
      right: 12px;
      top: 12px;
      background: var(--brand-yellow);
      color: #111;
      font-weight: 700;
      padding: .25rem .5rem;
      border-radius: 999px;
      font-size: .78rem;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
      z-index: 2
    }

    .media-wrap {
      width: 100%;
      max-width: 240px;
      aspect-ratio: 16/10;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: .15rem
    }

    .media-wrap .gif,
    .media-wrap img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 8px;
      display: block;
      background: #fff
    }

    .meta {
      margin-top: .35rem
    }

    .title {
      font-weight: 800;
      font-size: clamp(.98rem, 1.6vw, 1.05rem);
      color: var(--brand-dark);
      line-height: 1.05
    }

    .desc {
      color: var(--muted);
      font-size: clamp(.86rem, 1.2vw, .95rem);
      margin-top: .25rem
    }

    /* Animations */
    [data-animate] {
      opacity: 0;
      transform-origin: center;
      transition: opacity .6s var(--anim-ease), transform .6s var(--anim-ease);
      will-change: opacity, transform
    }

    [data-animate="fade-up"] {
      transform: translateY(14px)
    }

    [data-animate="slide-left"] {
      transform: translateX(18px)
    }

    [data-animate="slide-right"] {
      transform: translateX(-18px)
    }

    [data-animate="zoom-in"] {
      transform: scale(.96)
    }

    [data-animate].is-animated {
      opacity: 1;
      transform: none
    }

    [data-stagger="true"] [data-animate-child] {
      opacity: 0;
      transform: translateY(10px);
      transition: opacity .45s var(--anim-ease), transform .45s var(--anim-ease)
    }

    [data-animate].is-animated [data-animate-child] {
      opacity: 1;
      transform: translateY(0)
    }

    @media (prefers-reduced-motion:reduce) {

      [data-animate],
      [data-animate-child] {
        transition: none !important;
        transform: none !important;
        opacity: 1 !important
      }
    }

    @media(max-width:360px) {
      .delivery-grid {
        display: flex;
        gap: .75rem;
        overflow-x: auto;
        padding-bottom: .5rem;
        -webkit-overflow-scrolling: touch
      }

      .delivery-card {
        min-width: 260px;
        flex: 0 0 auto
      }
    }
  </style>
</head>

<body>

  <main class="page-container">

    <section class="hero">
      <div class="hero-card" aria-labelledby="about-hero-title" data-animate="fade-up" data-animate-delay="0">
        <span class="brand-badge" aria-hidden="true">
          <img src="../assets/LOGO.svg" alt="" width="18" height="18" style="object-fit:contain"> PackIT
        </span>

        <h1 id="about-hero-title" class="hero-title">Logistics made simple, fast, and affordable</h1>

        <p class="hero-lead">
          We’re a Filipino nationwide delivery partner dedicated to safe, reliable, and cost-efficient shipping across Luzon, Visayas, and Mindanao — from documents to larger freight.
        </p>

        <div class="features" aria-hidden="false" data-animate="fade-up" data-stagger="true" data-animate-delay="80">
          <div class="feature" data-animate-child>
            <div class="fi"><i class="bi bi-clock-history" aria-hidden="true"></i></div>
            <div>
              <h6>Fast Turnarounds</h6>
              <p>Optimized routes and local teams ensure quick delivery times.</p>
            </div>
          </div>

          <div class="feature" data-animate-child>
            <div class="fi"><i class="bi bi-shield-check" aria-hidden="true"></i></div>
            <div>
              <h6>Trusted & Secure</h6>
              <p>Every package is handled with care and tracked end-to-end.</p>
            </div>
          </div>

          <div class="feature" data-animate-child>
            <div class="fi"><i class="bi bi-wallet2" aria-hidden="true"></i></div>
            <div>
              <h6>Transparent Pricing</h6>
              <p>Competitive, simple pricing with no hidden fees.</p>
            </div>
          </div>
        </div>

        <div class="stats" role="region" aria-label="Key statistics" data-animate="slide-left" data-animate-delay="140">
          <div class="stat" data-animate-child>
            <h3 data-counter="+125">+125k</h3>
            <p>Deliveries completed</p>
          </div>
          <div class="stat" data-animate-child>
            <h3 data-counter="98">98%</h3>
            <p>On-time rate</p>
          </div>
          <div class="stat" data-animate-child>
            <h3 data-counter="24">24/7</h3>
            <p>Driver support</p>
          </div>
        </div>

        <div class="cta-strip" role="region" aria-label="Call to action" data-animate="fade-up" data-animate-delay="180">
          <a class="btn btn-warning btn-lg" href="tracking.php" role="button">Track Shipment</a>
        </div>
      </div>

      <div class="visual" data-animate="slide-right" data-animate-delay="60">
        <img src="../assets/gif1.gif" alt="PackIT delivery truck animation" loading="lazy" width="640" height="360" />
      </div>
    </section>

    <section class="mt-4">
      <div class="row gy-4 align-items-center">
        <div class="col-lg-6" data-animate="fade-up" data-animate-delay="40">
          <h2 class="h3 mb-3">About PackIT</h2>
          <p class="muted mb-3">
            PackIT started with a simple idea — make shipping accessible to everyone using technology and local expertise.
            From same-day courier needs to scheduled freight deliveries, our mission is to provide predictable outcomes and an exceptional customer experience.
          </p>

          <ul class="list-unstyled" data-animate="fade-up" data-animate-delay="60" data-stagger="true">
            <li class="mb-2" data-animate-child><strong>Extensive coverage:</strong> Luzon, Visayas & Mindanao.</li>
            <li class="mb-2" data-animate-child><strong>Real-time tracking:</strong> Updates at every step.</li>
            <li class="mb-2" data-animate-child><strong>Dedicated support:</strong> Reach us by chat, email, or phone.</li>
          </ul>
        </div>

        <div class="col-lg-6" data-animate="zoom-in" data-animate-delay="80">
          <img src="../assets/gif2.gif" alt="Warehouse animation" loading="lazy" class="rounded-lg" style="width:100%;max-height:360px;object-fit:cover;">
        </div>
      </div>
    </section>

    <section class="services" aria-label="Our Services">
      <h2 class="h3 mb-3" data-animate="fade-up">Our Services</h2>

      <div id="servicesCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3500" data-bs-pause="false" aria-label="Services carousel" data-animate="fade-up" data-animate-delay="40">
        <div class="carousel-inner">
          <div class="carousel-item active"><img src="../assets/carousel1.png" class="d-block w-100 rounded-lg" alt="Same-day delivery" loading="lazy"></div>
          <div class="carousel-item"><img src="../assets/carousel2.png" class="d-block w-100 rounded-lg" alt="Nationwide freight" loading="lazy"></div>
          <div class="carousel-item"><img src="../assets/carousel3.png" class="d-block w-100 rounded-lg" alt="Warehousing solutions" loading="lazy"></div>
          <div class="carousel-item"><img src="../assets/carousel4.png" class="d-block w-100 rounded-lg" alt="Ecommerce fulfillment" loading="lazy"></div>
          <div class="carousel-item"><img src="../assets/carousel5.png" class="d-block w-100 rounded-lg" alt="Custom logistics" loading="lazy"></div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#servicesCarousel" data-bs-slide="prev" aria-label="Previous service"><span class="carousel-control-prev-icon" aria-hidden="true"></span></button>
        <button class="carousel-control-next" type="button" data-bs-target="#servicesCarousel" data-bs-slide="next" aria-label="Next service"><span class="carousel-control-next-icon" aria-hidden="true"></span></button>
      </div>
    </section>

    <!-- HOW WE DELIVER -->
    <section class="how-we-deliver pb-5" aria-label="How we deliver">
      <div class="container">
        <h2 class="h3 mb-3" data-animate="fade-up">How We Deliver</h2>
        <p class="muted mb-3" data-animate="fade-up" data-animate-delay="30">
          We use a smart, multi-vehicle logistics system to match your package with the best delivery method.
        </p>

        <div class="row g-3" role="list" aria-label="Delivery methods" data-animate="fade-up" data-stagger="true" data-animate-delay="60">
          <div class="col-6 col-md-3" role="listitem">
            <div class="h-100 d-flex flex-column justify-content-between position-relative p-3 delivery-card" data-animate-child style="min-height:320px;">
              <span class="badge top-right">Express</span>
              <div class="media-wrap mx-auto"><img src="../assets/abt1.gif" alt="Motorcycle delivery — quick city drop" loading="lazy" class="gif"></div>
              <div class="meta">
                <div class="title">Fast City Drops</div>
                <div class="desc">Small parcels delivered in minutes</div>
              </div>
            </div>
          </div>

          <div class="col-6 col-md-3" role="listitem">
            <div class="h-100 d-flex flex-column justify-content-between position-relative p-3 delivery-card" data-animate-child style="min-height:320px;">
              <span class="badge top-right">Optimized</span>
              <div class="media-wrap mx-auto"><img src="../assets/abt2.gif" alt="Courier with map — optimized route and tracking" loading="lazy" class="gif"></div>
              <div class="meta">
                <div class="title">Smart Routing</div>
                <div class="desc">Optimized routes for faster, cheaper delivery</div>
              </div>
            </div>
          </div>

          <div class="col-6 col-md-3" role="listitem">
            <div class="h-100 d-flex flex-column justify-content-between position-relative p-3 delivery-card" data-animate-child style="min-height:320px;">
              <span class="badge top-right">Secure</span>
              <div class="media-wrap mx-auto"><img src="../assets/abt3.gif" alt="Courier handing package to customer — safe handoff" loading="lazy" class="gif"></div>
              <div class="meta">
                <div class="title">Safe Handoffs</div>
                <div class="desc">Contactless or verified handovers for security</div>
              </div>
            </div>
          </div>

          <div class="col-6 col-md-3" role="listitem">
            <div class="h-100 d-flex flex-column justify-content-between position-relative p-3 delivery-card" data-animate-child style="min-height:320px;">
              <span class="badge top-right">Guaranteed</span>
              <div class="media-wrap mx-auto"><img src="../assets/abt4.gif" alt="Clock icon — delivery on-time guarantee" loading="lazy" class="gif"></div>
              <div class="meta">
                <div class="title">On-time Guarantee</div>
                <div class="desc">Real-time ETA & status updates</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    (function() {
      const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      // Save original counter data and initialize visible counters to starting value (0)
      document.querySelectorAll('[data-counter]').forEach(el => {
        el.dataset._counterRaw = el.getAttribute('data-counter') || el.textContent || '';
        // set visible start value
        if (String(el.dataset._counterRaw).trim().startsWith('+')) el.textContent = '0+';
        else if (String(el.dataset._counterRaw).trim() === '24') el.textContent = '0';
        else el.textContent = '0';
      });

      function animateElement(el, delay = 0) {
        if (!el) return;
        if (prefersReduced) {
          el.classList.add('is-animated');
          return;
        }
        if (delay) el.style.transitionDelay = (delay / 1000) + 's';
        requestAnimationFrame(() => el.classList.add('is-animated'));
      }

      function clearElementAnimation(el) {
        if (!el) return;
        // remove class and transition delays so it can animate again
        el.classList.remove('is-animated');
        el.style.transitionDelay = '';
        // clear any inline transition delays on children
        el.querySelectorAll('[data-animate-child]').forEach(child => {
          child.classList.remove('is-animated');
          child.style.transitionDelay = '';
        });
      }

      function staggerChildren(container, baseDelay = 0, step = 80) {
        const children = Array.from(container.querySelectorAll('[data-animate-child]'));
        children.forEach((child, i) => {
          // set an inline delay then add class so they stagger in
          child.style.transitionDelay = ((baseDelay + i * step) / 1000) + 's';
          child.classList.add('is-animated');
        });
      }

      function resetCounters(container) {
        const counters = container.querySelectorAll('[data-counter]');
        counters.forEach(el => {
          const raw = el.dataset._counterRaw || '';
          if (String(raw).trim().startsWith('+')) el.textContent = '0+';
          else if (String(raw).trim() === '24') el.textContent = '0';
          else el.textContent = '0';
        });
      }

      function runCounters(container) {
        const counters = container.querySelectorAll('[data-counter]');
        counters.forEach(el => {
          const raw = el.getAttribute('data-counter') || el.textContent;
          const isPlus = String(raw).trim().startsWith('+');
          const numeric = parseInt(String(raw).replace(/\D/g, ''), 10) || 0;
          let start = 0;
          const duration = 900;
          const stepTime = 20;
          const steps = Math.max(1, Math.floor(duration / stepTime));
          const stepAmount = Math.max(1, Math.floor(numeric / steps));
          // ensure visible starts at 0 (or 0+)
          if (isPlus) el.textContent = '0+';
          else el.textContent = '0';
          const timer = setInterval(() => {
            start += stepAmount;
            if (start >= numeric) {
              start = numeric;
              clearInterval(timer);
            }
            if (isPlus) el.textContent = start + '+';
            else if (numeric === 24 && raw.indexOf('24/7') !== -1) el.textContent = (start === 24 ? '24/7' : start);
            else el.textContent = start;
          }, stepTime);
        });
      }

      // IntersectionObserver: toggle animation on enter/exit instead of unobserving
      const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          const el = entry.target;
          const delay = parseInt(el.dataset.animateDelay || '0', 10);

          if (entry.isIntersecting) {
            // element entered viewport — run animations
            if (el.dataset.stagger === 'true') {
              animateElement(el, delay / 2);
              // stagger children; they will get inline delays and classes
              staggerChildren(el, delay, 90);
            } else {
              animateElement(el, delay);
              const children = el.querySelectorAll('[data-animate-child]');
              if (children.length) {
                staggerChildren(el, delay + 80, 70);
              }
            }

            // run counters when element appears
            if (el.querySelectorAll && el.querySelectorAll('[data-counter]').length) {
              runCounters(el);
            }
          } else {
            // element left viewport — clear classes so animations can replay next time
            clearElementAnimation(el);
            // reset counters so they will replay next time; remove if you want counters to run only once
            if (el.querySelectorAll && el.querySelectorAll('[data-counter]').length) {
              resetCounters(el);
            }
          }
        });
      }, {
        threshold: 0.12
      });

      // Observe targets (or immediately mark as animated if reduced motion)
      document.querySelectorAll('[data-animate], [data-stagger]').forEach(el => {
        if (prefersReduced) {
          el.classList.add('is-animated');
          if (el.querySelectorAll('[data-counter]').length) runCounters(el);
        } else {
          io.observe(el);
        }
      });

      // Also observe isolated children that aren't within a data-stagger container
      document.querySelectorAll('[data-animate-child]').forEach(child => {
        if (!child.closest('[data-stagger="true"]')) {
          io.observe(child);
        }
      });

      // re-run observation when returning from bfcache
      window.addEventListener('pageshow', (e) => {
        if (e.persisted) {
          document.querySelectorAll('[data-animate]').forEach(el => {
            // remove so entrance logic can run again as user scrolls
            el.classList.remove('is-animated');
            if (!prefersReduced) io.observe(el);
          });
        }
      });
    })();
  </script>
</body>

</html>