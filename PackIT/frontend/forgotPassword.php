<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Forgot password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center min-vh-100" style="background-color: #fffef5;">
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
      <div class="card border-0 shadow-lg rounded-4 p-4">
        <div class="card-body">
          <h3 class="h5 fw-bold mb-3 text-center">Reset your password</h3>

          <?php if (!empty($_SESSION['fp_success'])): ?>
            <div class="alert alert-success small"><?= htmlspecialchars($_SESSION['fp_success']) ?></div>
            <?php unset($_SESSION['fp_success']); ?>
          <?php endif; ?>

          <?php if (!empty($_SESSION['fp_error'])): ?>
            <div class="alert alert-danger small"><?= htmlspecialchars($_SESSION['fp_error']) ?></div>
            <?php unset($_SESSION['fp_error']); ?>
          <?php endif; ?>

          <form action="forgotPasswordProcess.php" method="POST">
            <div class="mb-3">
              <label class="form-label small fw-semibold">Enter your account email</label>
              <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-warning fw-bold">Send reset link</button>
            </div>

            <div class="text-center mt-3">
              <a href="login.php" class="small text-decoration-none">Back to sign in</a>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>