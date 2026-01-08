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

// 3. Fetch Fresh User Data
require_once __DIR__ . '/../api/classes/User.php';
$userObj = new User();
$userDetails = $userObj->getUserDetails($_SESSION['user']['id']);

// If user not found (deleted?), redirect
if (!$userDetails) {
    header('Location: login.php');
    exit;
}

// 4. Map Data for Display
$displayName  = trim(($userDetails['first_name'] ?? '') . ' ' . ($userDetails['last_name'] ?? ''));
$email        = $userDetails['email'] ?? '';
$contact      = $userDetails['contact_number'] ?? '';
$profileImage = $userDetails['profile_image'] ?? null; // Now fetched from DB

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
    <title>Profile Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root { --brand-yellow: #fce354; }
        .profile-card { background-color: var(--brand-yellow); border-radius: 40px; }
        .profile-menu-container { border: 3px solid var(--brand-yellow); border-radius: 35px; }
        .avatar-container { width: 180px; height: 180px; border: 5px solid #fff; }
        .camera-btn { width: 40px; height: 40px; cursor: pointer; transition: transform 0.2s; }
        .camera-btn:hover { transform: scale(1.1); }
        .menu-btn { text-align: left; font-size: 1.1rem; color: #212529; text-decoration: none; }
        .menu-btn[aria-expanded="true"] .bi-chevron-down { transform: rotate(180deg); }
        .bi-chevron-down { transition: transform 0.2s; }
    </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-white">

<?php include("components/navbar.php"); ?>

<main class="container my-5 flex-grow-1">
    <div class="row g-4 align-items-center">

        <div class="col-12 col-lg-4">
            <div class="profile-card shadow-sm p-5 text-center position-relative h-100 d-flex flex-column align-items-center justify-content-center">
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success w-100 py-2 small mb-2"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger w-100 py-2 small mb-2"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
                <?php endif; ?>

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
                <small class="text-secondary d-block mb-3"><?= htmlspecialchars($displayAddress) ?></small>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="profile-menu-container p-4 p-md-5 bg-white h-100">
                
                <div class="mb-3 border-bottom pb-2">
                    <a class="menu-btn d-flex justify-content-between align-items-center w-100 py-2" 
                       data-bs-toggle="collapse" href="#accountCollapse" role="button" aria-expanded="false">
                        <span class="fw-medium">Account & Security</span>
                        <i class="bi bi-chevron-down small"></i>
                    </a>
                    <div class="collapse mt-2" id="accountCollapse">
                        <ul class="list-unstyled ms-3 mb-0">
                            <li><a href="changePassword.php" class="text-decoration-none text-secondary d-block py-1 hover-dark">Change Password</a></li>
                        </ul>
                    </div>
                </div>

                <div class="mb-3 border-bottom pb-2">
                    <a class="menu-btn d-flex justify-content-between align-items-center w-100 py-2" 
                       data-bs-toggle="collapse" href="#accessCollapse" role="button" aria-expanded="false">
                        <span class="fw-medium">Accessibility</span>
                        <i class="bi bi-chevron-down small"></i>
                    </a>
                    <div class="collapse mt-2" id="accessCollapse">
                        <p class="text-muted ms-3 small mb-0 py-1">Accessibility settings coming soon.</p>
                    </div>
                </div>

                <div class="mb-3 border-bottom pb-2">
                    <a class="menu-btn d-flex justify-content-between align-items-center w-100 py-2" 
                       data-bs-toggle="collapse" href="#feedbackCollapse" role="button" aria-expanded="false">
                        <span class="fw-medium">Feedback</span>
                        <i class="bi bi-chevron-down small"></i>
                    </a>
                    <div class="collapse mt-2" id="feedbackCollapse">
                        <ul class="list-unstyled ms-3 mb-0">
                            <li><a href="#" class="text-decoration-none text-secondary d-block py-1">Create Feedback</a></li>
                        </ul>
                    </div>
                </div>

                <div class="mb-4">
                    <a class="menu-btn d-flex justify-content-between align-items-center w-100 py-2" 
                       data-bs-toggle="collapse" href="#aboutCollapse" role="button" aria-expanded="false">
                        <span class="fw-medium">About</span>
                        <i class="bi bi-chevron-down small"></i>
                    </a>
                    <div class="collapse mt-2" id="aboutCollapse">
                        <ul class="list-unstyled ms-3 mb-0">
                            <li><a href="#" class="text-decoration-none text-secondary d-block py-1">App Version 1.0</a></li>
                            <li><a href="#" class="text-decoration-none text-secondary d-block py-1">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>

                <a href="logout.php" class="btn btn-warning w-100 fw-bold py-2 rounded-pill shadow-sm">Logout</a>
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
    const avatarSpinner = document.getElementById('avatarSpinner');

    fileInput.addEventListener('change', async function() {
        if (!this.files || !this.files[0]) return;

        // Show spinner
        profileDisplay.style.opacity = '0.5';
        avatarSpinner.classList.remove('d-none');

        const formData = new FormData();
        formData.append('avatar', this.files[0]);
        // Get token from the hidden input inside the form
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

        try {
            const response = await fetch('../api/user/update_avatar.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.ok) {
                // Update image immediately with the returned path + timestamp to force refresh
                profileDisplay.src = result.path + '?t=' + new Date().getTime();
            } else {
                alert(result.error || 'Failed to upload image');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while uploading.');
        } finally {
            // Hide spinner
            profileDisplay.style.opacity = '1';
            avatarSpinner.classList.add('d-none');
            // Clear input so selecting same file triggers change again
            fileInput.value = '';
        }
    });
</script>

</body>
</html>