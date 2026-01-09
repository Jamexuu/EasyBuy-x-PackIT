<?php
session_start();

// 1. Login Check
if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

// 2. CSRF Token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

// 3. Fetch Fresh User Data (Consistent with profile.php)
require_once __DIR__ . '/../api/classes/User.php';
$userObj = new User();
$userDetails = $userObj->getUserDetails($_SESSION['user']['id']);

if (!$userDetails) {
    header('Location: login.php');
    exit;
}

// 4. Map Data for Display
$displayName  = trim(($userDetails['first_name'] ?? '') . ' ' . ($userDetails['last_name'] ?? ''));
$email        = $userDetails['email'] ?? '';
$contact      = $userDetails['contact_number'] ?? '';
$profileImage = $userDetails['profile_image'] ?? null;

function formatAddress($u) {
    if (!$u) return '--';
    return implode(', ', array_filter([
        $u['house_number'] ?? null,
        $u['street'] ?? null,
        $u['subdivision'] ?? null,
        $u['barangay'] ?? null,
        $u['city'] ?? null,
        $u['province'] ?? null,
        $u['postal_code'] ?? null,
    ]));
}
$displayAddress = formatAddress($userDetails);

// Default SVG Avatar
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root { 
            --brand-yellow: #fce354; 
        }
        
        .profile-card {
            background-color: var(--brand-yellow);
            border-radius: 40px;
        }
        
        .profile-menu-container {
            border: 3px solid var(--brand-yellow);
            border-radius: 35px;
        }

        .avatar-container {
            width: 180px;
            height: 180px;
            border: 5px solid #fff;
        }

        .camera-btn {
            width: 40px;
            height: 40px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .camera-btn:hover {
            transform: scale(1.1);
        }
        
        /* Form specific styles */
        .form-control:focus {
            box-shadow: none;
            border-color: var(--brand-yellow);
        }

        /* Password toggle button styling */
        .toggle-password {
            min-width: 46px;
            border: 0;
            background: transparent;
            color: #495057;
        }
        .input-group .form-control {
            /* keep original visual style */
            background-color: #f8f9fa;
        }
        /* Slight tweak so the input and button appear seamless */
        .input-group .form-control.rounded-4 {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        .input-group .toggle-password {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-white">

    <?php include("components/navbar.php"); ?>

    <main class="container my-5 flex-grow-1">
        <div class="row g-4 align-items-center">

            <div class="col-12 col-lg-4">
                <div class="profile-card shadow-sm p-5 text-center position-relative h-100 d-flex flex-column align-items-center justify-content-center">
                    
                    <div class="position-relative mb-3">
                        <div class="avatar-container rounded-circle overflow-hidden bg-light mx-auto">
                            <img id="profileDisplay" 
                                 src="<?= htmlspecialchars($profileImage ?: $defaultAvatar) ?>" 
                                 alt="Profile" 
                                 class="w-100 h-100 object-fit-cover"
                                 onerror="this.src='<?= $defaultAvatar ?>'">
                        </div>
                        
                        <div class="camera-btn position-absolute bottom-0 end-0 bg-white border border-2 border-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                             onclick="document.getElementById('fileInput').click()">
                             <i class="bi bi-camera-fill text-dark"></i>
                        </div>
                        
                        <div id="avatarSpinner" class="position-absolute top-50 start-50 translate-middle d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <form id="avatarForm" class="d-none" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="file" id="fileInput" name="avatar" accept="image/*">
                    </form>

                    <h2 class="fw-bold text-dark mb-1"><?= htmlspecialchars($displayName) ?></h2>
                    <p class="text-secondary mb-2"><?= htmlspecialchars($email) ?></p>
                    <h5 class="fw-medium text-dark mb-1"><?= htmlspecialchars($contact ?: '--') ?></h5>
                    <small class="text-secondary d-block"><?= htmlspecialchars($displayAddress) ?></small>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <div class="profile-menu-container p-4 p-lg-5 bg-white h-100">
                    
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h3 class="fw-bold m-0">Change Password</h3>
                        <a href="profile.php" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                            <i class="bi bi-arrow-left me-1"></i> Back to Profile
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
                            <div class="input-group">
                                <input id="current_password" type="password" name="current_password" class="form-control form-control-lg bg-light border-0 rounded-4" required placeholder="••••••••">
                                <button type="button" class="btn toggle-password" data-target="#current_password" aria-label="Show current password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-secondary text-uppercase ls-1">New Password</label>
                            <div class="input-group">
                                <input id="new_password" type="password" name="new_password" class="form-control form-control-lg bg-light border-0 rounded-4" required minlength="8" pattern="(?=.*[A-Za-z])(?=.*\d).{8,}" placeholder="Min 8 chars, letters & numbers">
                                <button type="button" class="btn toggle-password" data-target="#new_password" aria-label="Show new password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="form-text small mt-2 ms-1 text-muted">Must be at least 8 characters long and contain both letters and numbers.</div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-semibold small text-secondary text-uppercase ls-1">Confirm New Password</label>
                            <div class="input-group">
                                <input id="confirm_password" type="password" name="confirm_password" class="form-control form-control-lg bg-light border-0 rounded-4" required minlength="8" placeholder="••••••••">
                                <button type="button" class="btn toggle-password" data-target="#confirm_password" aria-label="Show confirm password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Avatar upload handling (unchanged)
        const fileInput = document.getElementById('fileInput');
        const profileDisplay = document.getElementById('profileDisplay');
        const avatarSpinner = document.getElementById('avatarSpinner');

        if (fileInput) {
            fileInput.addEventListener('change', async function() {
                if (!this.files || !this.files[0]) return;

                // Show spinner
                profileDisplay.style.opacity = '0.5';
                avatarSpinner.classList.remove('d-none');

                const formData = new FormData();
                formData.append('avatar', this.files[0]);
                formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

                try {
                    const response = await fetch('../api/user/update_avatar.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (response.ok && result.ok) {
                        profileDisplay.src = result.path + '?t=' + new Date().getTime();
                    } else {
                        alert(result.error || 'Failed to upload image');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while uploading.');
                } finally {
                    profileDisplay.style.opacity = '1';
                    avatarSpinner.classList.add('d-none');
                    fileInput.value = '';
                }
            });
        }

        // Password visibility toggle
        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetSelector = btn.getAttribute('data-target');
                if (!targetSelector) return;
                const input = document.querySelector(targetSelector);
                if (!input) return;

                const icon = btn.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    if (icon) {
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    }
                    btn.setAttribute('aria-label', 'Hide password');
                } else {
                    input.type = 'password';
                    if (icon) {
                        icon.classList.add('bi-eye');
                        icon.classList.remove('bi-eye-slash');
                    }
                    btn.setAttribute('aria-label', 'Show password');
                }
            });
        });
    </script>
</body>
</html>