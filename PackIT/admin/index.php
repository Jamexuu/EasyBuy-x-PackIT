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
<html lang="en" class="h-100">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PackIT – Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary h-100">
  <div class="container w-100 m-auto" style="max-width: 420px;"> <main class="card border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="card-body p-5">
        
        <div class="text-center mb-4">
          <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 72px; height: 72px;">
            <i class="bi bi-people-fill fs-2 text-dark"></i>
          </div>
          <h1 class="h3 fw-bold mb-1">Welcome Back, Admin!</h1>
          <p class="text-muted mb-0">Sign in to your PackIT admin account</p>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials'): ?>
          <div class="alert alert-danger py-2 mb-4 rounded-3 small" role="alert">
            Invalid admin credentials. Please check your email and password.
          </div>
        <?php endif; ?>

        <form method="POST" novalidate>
          <div class="mb-3">
            <label for="adminEmail" class="form-label small fw-semibold">Email address</label>
            <div class="input-group input-group-lg">
              <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope-fill"></i></span>
              <input
                id="adminEmail"
                name="adminEmail"
                type="email"
                class="form-control bg-white border-start-0 ps-0"
                placeholder="name@example.com"
                required
                autofocus>
            </div>
          </div>

          <div class="mb-3">
            <label for="adminPassword" class="form-label small fw-semibold">Password</label>
            <div class="input-group input-group-lg">
              <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock-fill"></i></span>
              <input
                id="adminPassword"
                name="adminPassword"
                type="password"
                class="form-control bg-white border-start-0 border-end-0 ps-0"
                placeholder="Enter your password"
                required
                autocomplete="current-password">
              <button
                id="togglePasswordBtn"
                type="button"
                class="btn btn-light border border-start-0 text-muted"
                title="Show password">
                <i id="toggleIcon" class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <div class="d-flex justify-content-end mb-4">
            <a href="#" class="small text-decoration-none text-muted link-primary">Forgot password?</a>
          </div>

          <div class="d-grid mb-4">
            <button type="submit" class="btn btn-warning btn-lg rounded-pill fw-bold shadow-sm">Sign In</button>
          </div>

          <div class="text-center small text-muted">
            Don't have an account? <a href="#" class="fw-semibold text-dark text-decoration-underline">Contact support</a>
          </div>
        </form>
      </div>

      <div class="card-footer bg-transparent border-0 text-center text-muted small pb-4">
        © <?= date('Y'); ?> PackIT — Admin Portal
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Pure JS for password toggle, standard logic
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('adminPassword');
    const toggleIcon = document.getElementById('toggleIcon');

    if (toggleBtn && passwordInput && toggleIcon) {
      toggleBtn.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        toggleIcon.classList.toggle('bi-eye');
        toggleIcon.classList.toggle('bi-eye-slash');
      });
    }
  </script>
</body>
</html>