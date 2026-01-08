<?php
session_start();

// ensure user logged in
if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

// Ensure CSRF token exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

$user = $_SESSION['user'];
$displayName = trim(($user['firstName'] ?? '') . ' ' . ($user['lastName'] ?? ''));
$email = $user['email'] ?? '';
$contact = $user['contact'] ?? '';
$profileImage = $user['profile_image'] ?? null;

// DB for address (same approach as profile.php)
$pdo = new PDO(
    "mysql:host=127.0.0.1;dbname=packit;charset=utf8mb4",
    "root",
    "",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

$stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$user['id']]);
$address = $stmt->fetch();

function formatAddress($addr) {
    if (!$addr) return '--';
    return implode(', ', array_filter([
        $addr['house_number'] ?? null,
        $addr['street'] ?? null,
        $addr['subdivision'] ?? null,
        $addr['barangay'] ?? null,
        $addr['city'] ?? null,
        $addr['province'] ?? null,
        $addr['postal_code'] ?? null,
    ]));
}

$displayAddress = formatAddress($address);

// Default avatar
$defaultAvatar = 'data:image/svg+xml;charset=UTF-8,' . rawurlencode(
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
        <circle cx="100" cy="100" r="100" fill="#e5e8ec"/>
        <circle cx="100" cy="78" r="40" fill="#6c757d"/>
        <path d="M35 170c0-36 29-55 65-55s65 19 65 55" fill="#6c757d"/>
    </svg>'
);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Change password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root { --brand-yellow:#fce354; }
    body { background:#fff; min-height:100vh; display:flex; flex-direction:column; }
    .profile-card { background:var(--brand-yellow); border-radius:40px; padding:50px 20px; text-align:center; }
    .profile-img-container { width:180px; height:180px; border-radius:50%; overflow:hidden; border:5px solid #fff; background:#eee; margin:auto; }
    #profileDisplay { width:100%; height:100%; object-fit:cover; }
    .camera-icon-button { position:absolute; bottom:5px; right:15px; width:40px; height:40px; background:#fff; border-radius:50%; border:2px solid var(--brand-yellow); display:flex; align-items:center; justify-content:center; cursor:pointer; }
    .menu-outline { border:3px solid var(--brand-yellow); border-radius:35px; padding:30px; }
    .form-card { border-radius:12px; }
    @media (min-width: 992px) {
      .form-card { margin-left: 0; }
    }
  </style>
</head>
<body>

<?php include("components/navbar.php"); ?>

<div class="container my-5">
  <div class="row g-5 align-items-start">
    <!-- LEFT: profile card (unchanged) -->
    <div class="col-lg-4">
      <div class="profile-card shadow-sm">
        <div class="position-relative" style="width:180px;margin:auto">
          <div class="profile-img-container">
            <img id="profileDisplay" src="<?= htmlspecialchars($profileImage ?: $defaultAvatar) ?>" onerror="this.src='<?= $defaultAvatar ?>'">
          </div>
          <div class="camera-icon-button" onclick="document.getElementById('fileInput').click()">📷</div>
        </div>

        <form id="avatarForm" class="d-none" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
          <input type="file" id="fileInput" name="avatar" accept="image/*">
        </form>

        <h2 class="fw-bold mt-3"><?= htmlspecialchars($displayName) ?></h2>
        <p class="text-muted"><?= htmlspecialchars($email) ?></p>
        <h5><?= htmlspecialchars($contact ?: '--') ?></h5>
        <h6><?= htmlspecialchars($displayAddress) ?></h6>
      </div>
    </div>

    <!-- RIGHT: standalone change-password card (NOT in menu area) -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm p-4 form-card">
        <h4 class="mb-3">Change password</h4>

        <?php if (!empty($_SESSION['success'])): ?>
          <div class="alert alert-success small"><?= htmlspecialchars($_SESSION['success']) ?></div>
          <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
          <div class="alert alert-danger small"><?= htmlspecialchars($_SESSION['error']) ?></div>
          <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="changePasswordProcess.php" novalidate>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Current password</label>
            <input type="password" name="current_password" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">New password</label>
            <input type="password" name="new_password" class="form-control" required minlength="6">
            <div class="form-text small">At least 6 characters.</div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Confirm new password</label>
            <input type="password" name="confirm_password" class="form-control" required minlength="6">
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-warning fw-bold">Update password</button>
          </div>

          <div class="text-center mt-3">
            <a href="profile.php" class="small text-decoration-none">Back to profile</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include("components/footer.php"); ?>
<?php include("../frontend/components/chat.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>