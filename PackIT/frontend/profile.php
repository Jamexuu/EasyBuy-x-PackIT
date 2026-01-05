<?php
session_start();

// If user is not logged in, redirect to login
if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

// CSRF token for uploads
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

// User data from session (set at login)
$user = $_SESSION['user'];
$displayName = trim(($user['firstName'] ?? '') . ' ' . ($user['lastName'] ?? ''));
$email = $user['email'] ?? '';
$contact = $user['contact'] ?? '';
$profileImageFromSession = $user['profile_image'] ?? null;

// Database connection settings (adjust to your environment if needed)
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_NAME = getenv('DB_NAME') ?: 'packit';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';

$address = null;
$profileImage = $profileImageFromSession ?: null;

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Fetch the latest address for this user (if any)
    $stmt = $pdo->prepare('SELECT * FROM addresses WHERE user_id = :uid ORDER BY id DESC LIMIT 1');
    $stmt->execute([':uid' => $user['id']]);
    $address = $stmt->fetch();

    // Fetch user profile_image from DB (source of truth)
    $stmt2 = $pdo->prepare('SELECT profile_image FROM users WHERE id = :uid LIMIT 1');
    $stmt2->execute([':uid' => $user['id']]);
    $row = $stmt2->fetch();
    if ($row && !empty($row['profile_image'])) {
        $profileImage = $row['profile_image'];
        $_SESSION['user']['profile_image'] = $profileImage; // keep session fresh
    }

} catch (Exception $e) {
    error_log('Profile DB error: ' . $e->getMessage());
    $address = null;
}

// Helper to format address for display
function formatAddress($addr) {
    if (!$addr) return '--';
    $parts = [];
    if (!empty($addr['house_number'])) $parts[] = $addr['house_number'];
    if (!empty($addr['street'])) $parts[] = $addr['street'];
    if (!empty($addr['subdivision'])) $parts[] = $addr['subdivision'];
    if (!empty($addr['barangay'])) $parts[] = $addr['barangay'];
    if (!empty($addr['city'])) $parts[] = $addr['city'];
    if (!empty($addr['province'])) $parts[] = $addr['province'];
    if (!empty($addr['postal_code'])) $parts[] = $addr['postal_code'];
    return $parts ? implode(", ", $parts) : '--';
}

$displayAddress = formatAddress($address);

// Embedded default avatar (no path issues)
$defaultAvatarDataUri = 'data:image/svg+xml;charset=UTF-8,' . rawurlencode(
    '<?xml version="1.0" encoding="UTF-8"?><svg width="200" height="200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"><circle cx="100" cy="100" r="100" fill="#e5e8ec"/><circle cx="100" cy="78" r="40" fill="#6c757d"/><path d="M35 170c0-36 29-55 65-55s65 19 65 55" fill="#6c757d"/></svg>'
);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root { --brand-yellow: #fce354; }

        body {
            background-color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .profile-card {
            background-color: var(--brand-yellow);
            border-radius: 40px;
            padding: 50px 20px;
            text-align: center;
            position: relative;
        }

        .profile-img-container {
            position: relative;
            width: 180px; height: 180px;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid white;
            background: #eee;
            margin: 0 auto;
        }

        #profileDisplay { width: 100%; height: 100%; object-fit: cover; }

        .camera-icon-button {
            position: absolute; bottom: 5px; right: 15px;
            background: white; width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; border: 2px solid var(--brand-yellow);
            transition: transform 0.2s; z-index: 10;
        }

        .menu-outline .logout-item { margin-top: 12px; }

        #userName[contenteditable="true"] {
            background-color: transparent !important; outline: none;
            border-bottom: 2px dashed rgba(0, 0, 0, 0.2); padding: 0 5px;
        }
        #userName:empty:not(:focus):before {
            content: "Enter Name"; color: rgba(0,0,0,0.3); font-weight: normal;
        }

        .status-link { text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center; transition: transform 0.2s ease; }
        .status-link:hover { transform: translateY(-5px); cursor: pointer; }
        .status-bubble {
            background-color: var(--brand-yellow);
            width: 80px; height: 80px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 8px; font-weight: bold; font-size: 1.2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .menu-outline { border: 3px solid var(--brand-yellow); border-radius: 35px; padding: 30px; margin-top: 30px; }
        .menu-link { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; color: #212529; text-decoration: none; font-size: 1.25rem; }
        .dropdown-item { font-size: 1rem; transition: all 0.2s; }
        .dropdown-item:hover { background-color: rgba(0,0,0,0.05); padding-left: 30px !important; }
        .menu-link .icon { display: inline-block; transition: transform 0.2s; }
        .menu-link[aria-expanded="true"] .icon { transform: rotate(180deg); }
    </style>
</head>

<body>
    <?php include("components/navbar.php"); ?>

    <div class="container my-5">
        <div class="row g-5">
            <div class="col-lg-4">
                <div class="profile-card shadow-sm">
                    <div class="position-relative mx-auto" style="width: 180px;">
                        <div class="profile-img-container shadow-sm">
                            <img
                                id="profileDisplay"
                                alt="Profile"
                                src="<?php echo htmlspecialchars($profileImage ?: $defaultAvatarDataUri); ?>"
                                onerror="this.src='<?php echo $defaultAvatarDataUri; ?>';"
                            >
                        </div>
                        <div class="camera-icon-button shadow" onclick="document.getElementById('fileInput').click();">
                            <span>📷</span>
                        </div>
                    </div>

                    <form id="avatarForm" method="post" enctype="multipart/form-data" class="d-none">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="file" id="fileInput" name="avatar" accept="image/*" onchange="handleAvatarUpload(event)">
                    </form>

                    <div class="d-flex align-items-center justify-content-center mt-3 mb-1">
                        <h2 id="userName" class="fw-bold mb-0" contenteditable="false" spellcheck="false"><?php echo htmlspecialchars($displayName ?: ''); ?></h2>
                        <button class="btn btn-link btn-sm text-dark p-0 ms-2 text-decoration-none" onclick="toggleEdit()">
                            <span id="editIcon">✎</span>
                        </button>
                    </div>

                    <p id="userEmail" class="text-muted small mb-4"><?php echo htmlspecialchars($email); ?></p>

                    <div class="mt-4">
                        <h4 class="mb-1 fw-normal"><?php echo htmlspecialchars($contact ?: '--'); ?></h4>
                        <h4 class="fw-normal"><?php echo htmlspecialchars($displayAddress); ?></h4>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="row text-center mb-4">
                    <div class="col-4">
                        <a href="#" class="status-link">
                            <div class="status-bubble">0</div>
                            <div class="status-label small fw-bold">To Receive</div>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="#" class="status-link">
                            <div class="status-bubble">0</div>
                            <div class="status-label small fw-bold">To Deliver</div>
                        </a>
                    </div>
                    <div class="col-4">
                        <a href="#" class="status-link">
                            <div class="status-bubble">0</div>
                            <div class="status-label small fw-bold">Cancelled</div>
                        </a>
                    </div>
                </div>

                <div class="menu-outline">
                    <a href="#collapseAccount" class="menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false">
                        <span>Account & Security</span> <span class="icon">▾</span>
                    </a>
                    <div class="collapse" id="collapseAccount">
                        <ul class="list-unstyled ps-4 pb-2 border-bottom mb-2">
                            <li><a class="dropdown-item py-2" href="#">Password Settings</a></li>
                            <li><a class="dropdown-item py-2" href="#">Two-Factor Auth</a></li>
                        </ul>
                    </div>

                    <a href="#collapseAccessibility" class="menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false">
                        <span>Accessibility</span> <span class="icon">▾</span>
                    </a>
                    <div class="collapse" id="collapseAccessibility">
                        <ul class="list-unstyled ps-4 pb-2 border-bottom mb-2">
                            <li><a class="dropdown-item py-2" href="#">Regular link</a></li>
                            <li><a class="dropdown-item py-2 active bg-warning text-dark rounded" href="#" aria-current="true">Active link</a></li>
                            <li><a class="dropdown-item py-2" href="#">Another link</a></li>
                        </ul>
                    </div>

                    <a href="#collapseFeedback" class="menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false">
                        <span>Feedback</span> <span class="icon">▾</span>
                    </a>
                    <div class="collapse" id="collapseFeedback">
                        <ul class="list-unstyled ps-4 pb-2 border-bottom mb-2">
                            <li><a class="dropdown-item py-2" href="#">Report a Bug</a></li>
                            <li><a class="dropdown-item py-2" href="#">Suggest a Feature</a></li>
                        </ul>
                    </div>

                    <a href="#collapseAbout" class="menu-link" data-bs-toggle="collapse" role="button" aria-expanded="false">
                        <span>About</span> <span class="icon">▾</span>
                    </a>
                    <div class="collapse" id="collapseAbout">
                        <ul class="list-unstyled ps-4 pb-2 border-bottom mb-2">
                            <li><a class="dropdown-item py-2" href="#">App Version 1.0</a></li>
                            <li><a class="dropdown-item py-2" href="#">Privacy Policy</a></li>
                        </ul>
                    </div>

                    <div class="logout-item">
                        <a class="btn btn-outline-dark w-100 mt-3" href="logout.php" role="button">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("components/footer.php"); ?>
    <?php include("../frontend/components/chat.php"); ?>

    <script>
        function toggleEdit() {
            const nameField = document.getElementById('userName');
            const editIcon = document.getElementById('editIcon');
            const isEditing = nameField.contentEditable === "true";

            if (!isEditing) {
                nameField.contentEditable = "true";
                nameField.focus();
                editIcon.innerText = "✔";
                editIcon.style.color = "green";
            } else {
                nameField.contentEditable = "false";
                editIcon.innerText = "✎";
                editIcon.style.color = "black";
            }
        }

        document.getElementById('userName').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                toggleEdit();
            }
        });

        function handleAvatarUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Preview immediately
            const reader = new FileReader();
            reader.onload = function () {
                document.getElementById('profileDisplay').src = reader.result;
            };
            reader.readAsDataURL(file);

            // Upload to server
            const form = document.getElementById('avatarForm');
            const formData = new FormData(form);
            formData.set('avatar', file);

            fetch('../api/user/update_avatar.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (res && res.ok) {
                    document.getElementById('profileDisplay').src = res.path;
                } else {
                    alert(res.error || 'Upload failed');
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while uploading.');
            });
        }

        // Keep arrow icons in sync with collapse state
        document.querySelectorAll('.menu-link').forEach(link => {
            const targetId = link.getAttribute('href') || link.dataset.bsTarget;
            const target = document.querySelector(targetId);
            const icon = link.querySelector('.icon');
            if (!target || !icon) return;

            const setIcon = () => {
                icon.textContent = link.getAttribute('aria-expanded') === 'true' ? '▴' : '▾';
            };
            target.addEventListener('show.bs.collapse', () => { link.setAttribute('aria-expanded', 'true'); setIcon(); });
            target.addEventListener('hide.bs.collapse', () => { link.setAttribute('aria-expanded', 'false'); setIcon(); });

            // initialize
            setIcon();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>