<?php
session_start();

// If user is not logged in, redirect to login
if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

// CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

$user = $_SESSION['user'];
$displayName = trim(($user['firstName'] ?? '') . ' ' . ($user['lastName'] ?? ''));
$email = $user['email'] ?? '';
$contact = $user['contact'] ?? '';
$profileImage = $user['profile_image'] ?? null;

// DB
$pdo = new PDO(
    "mysql:host=127.0.0.1;dbname=packit;charset=utf8mb4",
    "root",
    "",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

// Address
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
    <title>Profile Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root { 
            --brand-yellow: #fce354; 
        }
        
        /* Custom styles that complement Bootstrap */
        .profile-card {
            background-color: var(--brand-yellow);
            border-radius: 40px; /* Kept your specific radius */
        }
        
        .profile-menu-container {
            border: 3px solid var(--brand-yellow);
            border-radius: 35px; /* Kept your specific radius */
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

        .menu-btn {
            text-align: left;
            font-size: 1.1rem;
            color: #212529;
            text-decoration: none;
        }
        
        /* Rotate chevron on expand */
        .menu-btn[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }
        .bi-chevron-down {
            transition: transform 0.2s;
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
            <div class="profile-menu-container p-4 p-md-5 bg-white">
                
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

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profileDisplay.src = e.target.result;
                // Here you would typically submit the form via AJAX
                // document.getElementById('avatarForm').submit(); 
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>

</body>
</html>