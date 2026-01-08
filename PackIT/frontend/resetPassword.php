<?php
session_start();
require_once __DIR__ . '/../api/classes/User.php';

// Check session-based OTP verification
$userId = $_SESSION['password_reset_user_id'] ?? null;
if (!$userId) {
    $_SESSION['fp_error'] = 'Please verify the one-time code first.';
    header('Location: forgotPassword.php');
    exit;
}

// token is valid; show password form
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Set new password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center min-vh-100" style="background-color: #fffef5;">
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
      <div class="card border-0 shadow-lg rounded-4 p-4">
        <div class="card-body">
          <h3 class="h5 fw-bold mb-3 text-center">Choose a new password</h3>

          <?php if (!empty($_SESSION['fp_error'])): ?>
            <div class="alert alert-danger small"><?= htmlspecialchars($_SESSION['fp_error']) ?></div>
            <?php unset($_SESSION['fp_error']); ?>
          <?php endif; ?>

          <form action="resetPasswordProcess.php" method="POST">
            <div class="mb-3">
              <label class="form-label small fw-semibold">New password</label>
              <input type="password" name="password" class="form-control" required minlength="6" placeholder="Enter new password">
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold">Confirm password</label>
              <input type="password" name="confirm_password" class="form-control" required minlength="6" placeholder="Confirm new password">
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-warning fw-bold">Set password</button>
            </div>
          </form>

          <div class="text-center mt-3">
            <a href="login.php" class="small text-decoration-none">Back to sign in</a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>