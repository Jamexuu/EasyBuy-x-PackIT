<?php
session_start();
require_once __DIR__ . '/../api/classes/Database.php';

$db = new Database();
$error = "";

// If already logged in as a customer, redirect to profile/dashboard
if (!empty($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = "Please enter email and password.";
    } else {
        // Look up the user by email
        $stmt = $db->executeQuery(
            "SELECT id, first_name, last_name, email, password, role FROM users WHERE email = ? LIMIT 1",
            [$email]
        );
        $rows = $db->fetch($stmt);

        if (empty($rows)) {
            // Generic message to avoid leaking which emails exist
            $error = "Invalid email or password.";
        } else {
            $user = $rows[0];
            if (!password_verify($password, $user['password'])) {
                $error = "Invalid email or password.";
            } else {
                // Successful login: regenerate session id and set session vars
                session_regenerate_id(true);

                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['user_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'] ?? 'user';

                // If an intended destination was saved before login, redirect there
                $redirect = $_SESSION['post_login_redirect'] ?? null;
                if ($redirect) {
                    unset($_SESSION['post_login_redirect']);
                    header("Location: " . $redirect);
                } else {
                    header("Location: profile.php");
                }
                exit;
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PackIT - Customer Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root { --brand-yellow:#fce354; }

        body {
            background: linear-gradient(180deg, #fffef5 0%, #ffffff 65%);
        }

        .login-card {
            border: 0;
            border-radius: 22px;
        }

        .brand-circle {
            width:64px;
            height:64px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            background: var(--brand-yellow);
            box-shadow: 0 10px 25px rgba(0,0,0,.08);
            margin: 0 auto 14px;
        }

        .brand-circle i {
            font-size: 1.8rem;
            color: #111;
        }

        .form-control.bg-light {
            border: 1px solid rgba(0,0,0,.08);
        }
    </style>
</head>

<body class="d-flex align-items-center min-vh-100">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg login-card p-4">
                <div class="card-body">

                    <div class="brand-circle">
                        <i class="bi bi-people-fill"></i>
                    </div>

                    <h1 class="h3 fw-bold mb-1 text-center">Customer Login</h1>
                    <p class="text-muted small text-center mb-4">
                        Sign in to your PackIT account
                    </p>

                    <?php if (!empty($_SESSION['success'])): ?>
                        <div class="alert alert-success small">
                            <?= htmlspecialchars((string)$_SESSION['success']) ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger small">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" novalidate>

                        <!-- EMAIL -->
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold">Email address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email"
                                       class="form-control bg-light"
                                       name="email"
                                       id="email"
                                       placeholder="name@example.com"
                                       required>
                            </div>
                        </div>

                        <!-- PASSWORD WITH EYE ICON -->
                        <div class="mb-3">
                            <label for="password" class="form-label small fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-lock"></i>
                                </span>

                                <input type="password"
                                       class="form-control bg-light"
                                       name="password"
                                       id="password"
                                       placeholder="Enter your password"
                                       required>

                                <button class="btn btn-light border"
                                        type="button"
                                        id="togglePassword"
                                        aria-label="Toggle password visibility">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <a href="forgotPassword.php" class="small text-decoration-none">
                                    Forgot password?
                                </a>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-warning btn-lg rounded-pill">Sign In</button>
                        </div>

                    </form>

                    <div class="text-center mt-3">
                        <p class="small text-muted mb-0">
                            Don't have an account?
                            <a href="signUp.php" class="text-dark fw-bold">Sign up</a>
                        </p>
                    </div>

                </div>
            </div>

            <div class="text-center mt-4">
                <a href="../index.php" class="text-decoration-none text-muted small">&larr; Back to Home</a>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    (function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        if (!togglePassword || !passwordInput) return;

        const icon = togglePassword.querySelector('i');

        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            if (icon) {
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            }
        });
    })();
</script>

</body>
</html>