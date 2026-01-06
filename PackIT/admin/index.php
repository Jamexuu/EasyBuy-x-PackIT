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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PackIT – Admin Login</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
    }

    /* Keeps the card visually compact on large screens */
    .login-card {
      max-width: 420px;
      margin: 0 auto;
    }
  </style>
</head>

<body class="d-flex flex-column min-vh-100">

<!-- NAVBAR -->
<?php
$page = basename($_SERVER['PHP_SELF']);
include __DIR__ . '/../frontend/components/navbar.php';
?>

<!-- MAIN CONTENT -->
<main class="flex-grow-1 py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-9 col-md-5 col-lg-4">

        <div class="card border-0 shadow-sm rounded-4 login-card">
          <div class="card-body p-4 p-md-5">

            <div class="text-center mb-4">
              <h4 class="fw-bold mb-1">Admin Sign In</h4>
              <p class="text-muted small mb-0">Authorized personnel only</p>
            </div>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials'): ?>
              <div class="alert alert-danger py-2 small text-center">
                Invalid admin credentials
              </div>
            <?php endif; ?>

            <form method="POST" novalidate>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Email address</label>
                <input
                  type="email"
                  class="form-control form-control-lg"
                  name="adminEmail"
                  placeholder="admin@packit.com"
                  required
                  autofocus>
              </div>

              <div class="mb-4">
                <label class="form-label small fw-semibold">Password</label>
                <input
                  type="password"
                  class="form-control form-control-lg"
                  name="adminPassword"
                  placeholder="••••••••"
                  required>
              </div>

              <div class="d-grid mb-3">
                <button type="submit" class="btn btn-warning btn-lg fw-bold">
                  Sign In
                </button>
              </div>

              <div class="text-center">
                <a href="#" class="small text-decoration-none text-muted">
                  Forgot password?
                </a>
              </div>

            </form>
          </div>

          <div class="card-footer bg-white border-0 text-center small text-muted">
            © <?= date('Y'); ?> PackIT — Admin Portal
          </div>
        </div>

      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<?php
$page = basename($_SERVER['PHP_SELF']);
include __DIR__ . '/../frontend/components/footer.php';
?>
</body>
</html>
