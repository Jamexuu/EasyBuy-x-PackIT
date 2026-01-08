<?php
session_start();

// Ensure user is logged in
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

// Database Connection
$pdo = new PDO(
    "mysql:host=127.0.0.1;dbname=packit;charset=utf8mb4",
    "root",
    "",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

// Fetch Address
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

// Default Avatar
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
    <title>Change Password</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-white d-flex flex-column min-vh-100">

    <?php include("components/navbar.php"); ?>

    <main class="container my-5 flex-grow-1">
        <div class="row g-4 align-items-center justify-content-center h-100">

            <div class="col-12 col-lg-4">
                <div class="text-center p-5 shadow-sm d-flex flex-column align-items-center" 
                     style="background-color: #fce354; border-radius: 40px;">
                    
                    <div class="position-relative mb-3" style="width: 180px;">
                        <div class="overflow-hidden rounded-circle border border-5 border-white bg-light shadow-sm" 
                             style="width: 180px; height: 180px;">
                            <img id="profileDisplay"
                                 src="<?= htmlspecialchars($profileImage ?: $defaultAvatar) ?>"
                                 class="w-100 h-100 object-fit-cover"
                                 onerror="this.src='<?= $defaultAvatar ?>'"
                                 alt="Profile">
                        </div>
                        
                        <div class="position-absolute bg-white border border-2 rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                             style="width: 40px; height: 40px; bottom: 5px; right: 10px; border-color: #fce354 !important; cursor: pointer;" 
                             onclick="document.getElementById('fileInput').click()">
                            <i class="bi bi-camera-fill text-dark"></i>
                        </div>
                    </div>

                    <form id="avatarForm" class="d-none" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="file" id="fileInput" name="avatar" accept="image/*">
                    </form>

                    <h2 class="fw-bold mt-3 text-dark text-break"><?= htmlspecialchars($displayName) ?></h2>
                    <p class="text-secondary mb-2 text-break"><?= htmlspecialchars($email) ?></p>
                    <h5 class="fw-medium mb-1 text-dark"><?= htmlspecialchars($contact ?: '--') ?></h5>
                    <h6 class="text-secondary small text-break"><?= htmlspecialchars($displayAddress) ?></h6>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <div class="p-4 p-lg-5" style="border: 3px solid #fce354; border-radius: 35px;">
                    
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h3 class="fw-bold m-0">Change Password</h3>
                        <a href="profile.php" class="btn btn-outline-secondary rounded-pill btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </a>
                    </div>

                    <?php if (!empty($_SESSION['success'])): ?>
                        <div class="alert alert-success rounded-4 small border-0 shadow-sm mb-4">
                            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger rounded-4 small border-0 shadow-sm mb-4">
                            <i class="bi bi-exclamation-circle-fill me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="POST" action="changePasswordProcess.php" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-secondary text-uppercase ls-1">Current Password</label>
                            <input type="password" name="current_password" class="form-control form-control-lg bg-light border-0 rounded-4" required placeholder="••••••••">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-secondary text-uppercase ls-1">New Password</label>
                            <input type="password" name="new_password" class="form-control form-control-lg bg-light border-0 rounded-4" required minlength="8" pattern="(?=.*[A-Za-z])(?=.*\d).{8,}" placeholder="Min 8 chars, letters & numbers">
                            <div class="form-text small mt-2 ms-1 text-muted">Must be at least 8 characters long and contain both letters and numbers.</div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-semibold small text-secondary text-uppercase ls-1">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control form-control-lg bg-light border-0 rounded-4" required minlength="8" placeholder="••••••••">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning py-3 rounded-pill fw-bold shadow-sm" style="background-color: #fce354; border: none;">
                                Update Password
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </main>

    <?php include("components/footer.php"); ?>
    <?php include("../frontend/components/chat.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const fileInput = document.getElementById('fileInput');
        const profileDisplay = document.getElementById('profileDisplay');

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileDisplay.src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>