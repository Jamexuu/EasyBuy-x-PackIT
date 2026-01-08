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
stmt:
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root { --brand-yellow:#fce354; }

        body {
            background:#fff;
            min-height:100vh;
            display:flex;
            flex-direction:column;
        }

        .profile-card {
            background:var(--brand-yellow);
            border-radius:40px;
            padding:50px 20px;
            text-align:center;
        }

        .profile-img-container {
            width:180px;
            height:180px;
            border-radius:50%;
            overflow:hidden;
            border:5px solid #fff;
            background:#eee;
            margin:auto;
        }

        #profileDisplay {
            width:100%;
            height:100%;
            object-fit:cover;
        }

        .camera-icon-button {
            position:absolute;
            bottom:5px;
            right:15px;
            width:40px;
            height:40px;
            background:#fff;
            border-radius:50%;
            border:2px solid var(--brand-yellow);
            display:flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
        }

        .menu-outline {
            border:3px solid var(--brand-yellow);
            border-radius:35px;
            padding:30px;
            /* removed fixed top margin so it vertically centers with the profile card */
            /* margin-top:96px; */
        }

        .menu-link {
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:12px 0;
            text-decoration:none;
            font-size:1.2rem;
            color:#212529;
        }

        .menu-link .icon {
            transition:.2s;
        }

        .menu-link[aria-expanded="true"] .icon {
            transform:rotate(180deg);
        }

        .dropdown-item:hover {
            background:rgba(0,0,0,.05);
            padding-left:30px !important;
        }
    </style>
</head>

<body>

<?php include("components/navbar.php"); ?>

<div class="container my-5">
    <!-- Added align-items-center so the two columns vertically align with each other -->
    <div class="row g-5 align-items-center">

        <!-- LEFT -->
        <div class="col-lg-4">
            <div class="profile-card shadow-sm">
                <div class="position-relative" style="width:180px;margin:auto">
                    <div class="profile-img-container">
                        <img id="profileDisplay"
                             src="<?= htmlspecialchars($profileImage ?: $defaultAvatar) ?>"
                             onerror="this.src='<?= $defaultAvatar ?>'">
                    </div>
                    <div class="camera-icon-button" onclick="fileInput.click()">📷</div>
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

        <!-- RIGHT -->
        <div class="col-lg-8">
            <div class="menu-outline">

                <!-- Account -->
                <a class="menu-link" data-bs-toggle="collapse" href="#account">
                    Account & Security <span class="icon">▾</span>
                </a>
                <div class="collapse" id="account">
                    <!-- Link now points to change_password.php (no UI changes) -->
                    <a class="dropdown-item py-2" href="changePassword.php">Change Password</a>
                </div>

                <!-- Accessibility (RESTORED) -->
                <a class="menu-link mt-3" data-bs-toggle="collapse" href="#accessibility">
                    Accessibility <span class="icon">▾</span>
                </a>
                <!-- Feedback -->
                <a class="menu-link mt-3" data-bs-toggle="collapse" href="#feedback">
                    Feedback <span class="icon">▾</span>
                </a>
                <div class="collapse" id="feedback">
                    <a class="dropdown-item py-2" href="#">Create Feedback</a>
                </div>

                <!-- About -->
                <a class="menu-link mt-3" data-bs-toggle="collapse" href="#about">
                    About <span class="icon">▾</span>
                </a>
                <div class="collapse" id="about">
                    <a class="dropdown-item py-2" href="#">App Version 1.0</a>
                    <a class="dropdown-item py-2" href="#">Privacy Policy</a>
                </div>

                <a href="logout.php" class="btn btn-warning w-100 mt-4">Logout</a>
            </div>
        </div>

    </div>
</div>

<?php include("components/footer.php"); ?>
<?php include("../frontend/components/chat.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>