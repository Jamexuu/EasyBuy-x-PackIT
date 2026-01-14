<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Enter OTP</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center min-vh-100" style="background-color: #fffef5;">
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
      <div class="card border-0 shadow-lg rounded-4 p-4">
        <div class="card-body">
          <h3 class="h5 fw-bold mb-3 text-center">Enter one-time code</h3>

          <?php if (!empty($_SESSION['fp_error'])): ?>
            <div class="alert alert-danger small"><?= htmlspecialchars($_SESSION['fp_error']) ?></div>
            <?php unset($_SESSION['fp_error']); ?>
          <?php endif; ?>

          <?php if (!empty($_SESSION['fp_success'])): ?>
            <div class="alert alert-success small"><?= htmlspecialchars($_SESSION['fp_success']) ?></div>
            <?php unset($_SESSION['fp_success']); ?>
          <?php endif; ?>

          <form action="verifyOTPProcess.php" method="POST">
            <div class="mb-3">
              <label class="form-label small fw-semibold">Email</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label small fw-semibold">One-time code</label>
              <input type="text" name="otp" class="form-control" required pattern="\d{4,8}" maxlength="8" placeholder="Enter the code you received">
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-warning fw-bold">Verify code</button>
            </div>
          </form>

          <form action="forgotPasswordProcess.php" method="POST" class="text-center mt-3">
            <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
            <span class="small text-muted">Didn't receive the code?</span>
            <button type="submit" id="resendBtn" class="btn btn-link btn-sm p-0 align-baseline text-decoration-none fw-bold" disabled>
              Resend OTP (60s)
            </button>
          </form>

          <div class="text-center mt-3">
            <a href="forgotPassword.php" class="small text-decoration-none">Back</a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Simple 60-second countdown timer for the Resend button
  (function() {
    const resendBtn = document.getElementById('resendBtn');
    if (!resendBtn) return;

    let timeLeft = 60;
    
    // Function to update button state
    const updateTimer = () => {
      if (timeLeft <= 0) {
        resendBtn.disabled = false;
        resendBtn.innerText = "Resend OTP";
        resendBtn.classList.remove('text-secondary'); 
      } else {
        resendBtn.disabled = true;
        resendBtn.innerText = `Resend OTP (${timeLeft}s)`;
        resendBtn.classList.add('text-secondary');
        timeLeft--;
        setTimeout(updateTimer, 1000);
      }
    };

    // Start timer on page load
    updateTimer();
  })();
</script>
</body>
</html>