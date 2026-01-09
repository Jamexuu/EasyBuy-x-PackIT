<?php
require_once '../api/classes/Auth.php';
require_once '../api/classes/User.php';

Auth::redirectIfAdminLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['adminEmail'] ?? '';
    $password = $_POST['adminPassword'] ?? '';

    $user = new User();
    $result = $user->login($email, $password, 'admin');

    if ($result) {
        Auth::login($result['id'], $result['email'], $result['first_name'], 'admin');
        header("Location: dashboard.php");
        exit();
    }

    header("Location: index.php?error=invalid_credentials");
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PackIT – Admin Login</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root{
      --brand-yellow: #f8e15b;
      --brand-dark: #111217;
      --muted: #6b7280;
      --card-radius: 20px;
      --card-shadow: 0 18px 50px rgba(16,24,40,0.08);
      --input-bg: #f4f5f7;
      --input-radius: 12px;
    }

    html,body { height:100%; }
    body {
      font-family: Inter, "Segoe UI", system-ui, -apple-system, "Helvetica Neue", Arial;
      background: linear-gradient(180deg, #fbfbfb 0%, #fff 100%);
      color: var(--brand-dark);
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
      margin:0;
    }

    /* center page */
    .page-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }

    .login-card {
      width: 100%;
      max-width: 420px;
      border-radius: var(--card-radius);
      box-shadow: var(--card-shadow);
      overflow: hidden;
      background: linear-gradient(180deg, #fff 0%, #fff 100%);
    }

    .card-body {
      padding: 2.25rem;
    }

    /* Top avatar badge */
    .badge-circle {
      width:72px;
      height:72px;
      background: var(--brand-yellow);
      border-radius:50%;
      display:inline-grid;
      place-items:center;
      margin: 0 auto 1rem auto;
      box-shadow: 0 6px 20px rgba(16,24,40,0.08);
    }
    .badge-circle i { font-size:1.5rem; color:var(--brand-dark); }

    .welcome-title {
      font-size:1.5rem;
      font-weight:800;
      margin-bottom:.25rem;
      text-align:center;
    }
    .welcome-sub {
      text-align:center;
      color:var(--muted);
      margin-bottom:1rem;
      font-size:.95rem;
    }

    /* input styles */
    .form-control-lg {
      height:48px;
      padding-left: .75rem;
    }

    .input-group .input-group-text {
      background: var(--input-bg);
      border: 0;
      min-width:44px;
      justify-content:center;
      color:var(--muted);
    }

    /* Password group: make the 3 parts behave as a single rounded control */
    .password-group .input-group-text:first-child {
      border-top-left-radius: var(--input-radius);
      border-bottom-left-radius: var(--input-radius);
      border-right: 0;
    }

    .password-group .form-control.form-control-lg {
      border-radius: 0;
      border: 0;
      background: white; /* keep the main input white to match design */
      padding-left: .5rem;
    }

    .password-group .btn-toggle {
      border: 0;
      background: var(--input-bg);
      color: var(--muted);
      width:44px;
      height:44px;
      display:inline-grid;
      place-items:center;
      cursor: pointer;
      border-top-right-radius: var(--input-radius);
      border-bottom-right-radius: var(--input-radius);
      margin-left: 0;
    }

    /* remove focus ring overlap and give subtle focus */
    .password-group .btn-toggle:focus, .password-group .form-control:focus, .password-group .input-group-text:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(248,225,91,0.15);
      z-index: 1;
    }

    /* primary sign-in button - rounded pill and full width */
    .btn-signin {
      background: var(--brand-yellow);
      color: var(--brand-dark);
      font-weight: 700;
      border: none;
      height:56px;
      border-radius: 999px;
      box-shadow: 0 10px 30px rgba(16,24,40,0.08);
      transition: transform .14s ease, box-shadow .14s ease;
    }
    .btn-signin:hover { transform: translateY(-3px); box-shadow: 0 18px 44px rgba(16,24,40,0.12); }

    .helper-links { font-size:.9rem; }
    .helper-links a { color: var(--muted); text-decoration: none; }
    .helper-links a:hover { text-decoration: underline; color: var(--brand-dark); }

    .card-footer {
      background: transparent;
      border-top: 0;
      text-align: center;
      font-size: .85rem;
      color: var(--muted);
      padding: 1rem 1.5rem 1.75rem;
    }

    /* small alert */
    .alert-small { padding: .5rem .75rem; margin-bottom: 1rem; font-size:.92rem; border-radius: 8px; }

    @media (max-width: 420px) {
      .card-body { padding: 1.5rem; }
      .badge-circle { width:64px; height:64px; }
    }
  </style>
</head>

<body>
  <div class="page-wrapper">
    <div class="login-card shadow-sm" role="region" aria-labelledby="loginTitle">
      <div class="card-body">
        <div class="text-center">
          <div class="badge-circle" aria-hidden="true">
            <i class="bi bi-people-fill"></i>
          </div>

          <h1 id="loginTitle" class="welcome-title">Welcome Back, Admin!</h1>
          <p class="welcome-sub">Sign in to your PackIT admin account</p>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials'): ?>
          <div class="alert alert-danger alert-small" role="alert">
            Invalid admin credentials. Please check your email and password.
          </div>
        <?php endif; ?>

        <form method="POST" novalidate aria-describedby="login-desc">
          <div class="mb-3">
            <label for="adminEmail" class="form-label small fw-semibold">Email address</label>
            <div class="input-group">
              <span class="input-group-text" id="emailAddon"><i class="bi bi-envelope-fill"></i></span>
              <input
                id="adminEmail"
                name="adminEmail"
                type="email"
                class="form-control form-control-lg"
                placeholder="name@example.com"
                aria-label="Admin email"
                aria-describedby="emailAddon"
                required
                autofocus>
            </div>
          </div>

          <div class="mb-3">
            <label for="adminPassword" class="form-label small fw-semibold">Password</label>

            <div class="input-group password-group">
              <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>

              <input
                id="adminPassword"
                name="adminPassword"
                type="password"
                class="form-control form-control-lg"
                placeholder="Enter your password"
                aria-label="Admin password"
                required
                autocomplete="current-password">

              <!-- make the toggle a button styled to match input-group-text background -->
              <button
                id="togglePasswordBtn"
                type="button"
                class="btn btn-toggle"
                title="Show password"
                aria-pressed="false"
                aria-label="Show password">
                <i id="toggleIcon" class="bi bi-eye" aria-hidden="true"></i>
              </button>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-4 helper-links">
            <div></div>
            <div><a href="#" class="small">Forgot password?</a></div>
          </div>

          <div class="d-grid mb-3">
            <button type="submit" class="btn btn-signin" aria-label="Sign in">Sign In</button>
          </div>

          <div class="text-center" id="login-desc" style="font-size:.92rem; color:var(--muted);">
            Don't have an account? <a href="#" class="fw-semibold" style="color:var(--brand-dark); text-decoration:underline;">Contact support</a>
          </div>
        </form>
      </div>

      <div class="card-footer">
        © <?= date('Y'); ?> PackIT — Admin Portal
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    (function () {
      const toggleBtn = document.getElementById('togglePasswordBtn');
      const passwordInput = document.getElementById('adminPassword');
      const toggleIcon = document.getElementById('toggleIcon');

      if (!toggleBtn || !passwordInput || !toggleIcon) return;

      function updateToggleState(isShowing) {
        toggleBtn.setAttribute('aria-pressed', String(isShowing));
        toggleBtn.setAttribute('title', isShowing ? 'Hide password' : 'Show password');
        toggleBtn.setAttribute('aria-label', isShowing ? 'Hide password' : 'Show password');

        if (isShowing) {
          toggleIcon.classList.remove('bi-eye');
          toggleIcon.classList.add('bi-eye-slash');
        } else {
          toggleIcon.classList.remove('bi-eye-slash');
          toggleIcon.classList.add('bi-eye');
        }
      }

      toggleBtn.addEventListener('click', function () {
        const isCurrentlyPassword = passwordInput.type === 'password';
        passwordInput.type = isCurrentlyPassword ? 'text' : 'password';
        updateToggleState(isCurrentlyPassword);
        // keep focus on the password input after toggling
        passwordInput.focus();
      });

      // allow Enter or Space key on toggle for accessibility
      toggleBtn.addEventListener('keydown', function(e){
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          toggleBtn.click();
        }
      });

      // initialize from default
      updateToggleState(false);
    })();
  </script>
</body>
</html>