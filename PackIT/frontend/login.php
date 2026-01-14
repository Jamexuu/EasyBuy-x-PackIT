<?php
session_start();

require_once __DIR__ . '/../api/classes/Auth.php';

// Redirect if already logged in
Auth::redirectIfLoggedIn();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PackIT - Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .easybuy-btn {
            background: #38ce13;
            color: #000;
            border-radius: 999px;
            padding: .6rem 1rem;
            font-weight: 600;
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

                    <h1 class="h3 fw-bold mb-1 text-center">Welcome Back</h1>
                    <p class="text-muted small text-center mb-4">
                        Sign in to your PackIT account
                    </p>

                    <!-- Optional: success messages stored in session -->
                    <?php if (!empty($_SESSION['success'])): ?>
                        <div class="alert alert-success small">
                            <?= htmlspecialchars((string)$_SESSION['success']) ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <!-- Client-side error box -->
                    <div id="errorBox" class="alert alert-danger small d-none"></div>

                    <form action="" method="POST" novalidate onsubmit="loginWithApi(event); return false;">

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

                    <!-- EasyBuy Sign-in (uses PackIT account) -->
                    <div class="text-center mt-3">
                        <p class="small text-muted mb-1">Or</p>
                        <a href="easyBuyLogin.php" class="btn easybuy-btn w-100">
                            <i class="bi bi-box-seam me-2"></i> Sign in with PackIT (EasyBuy)
                        </a>
                    </div>

                </div>
            </div>

            <div class="text-center mt-4">
                <a href="../index.php" class="text-decoration-none text-muted small">&larr; Back to Home</a>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

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

    function showError(message) {
        const box = document.getElementById('errorBox');
        box.textContent = message || 'Login failed.';
        box.classList.remove('d-none');
    }

    function clearError() {
        const box = document.getElementById('errorBox');
        box.textContent = '';
        box.classList.add('d-none');
    }

    async function loginWithApi(event) {
        event.preventDefault();
        clearError();

        const payload = {
            email: document.getElementById('email').value,
            password: document.getElementById('password').value
        };

        try {
            const res = await fetch('../api/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok || !data.success) {
                showError(data.error || 'Invalid email or password.');
                return;
            }

            // Role-based redirect (same behavior as your old PHP login)
            const role = data.role || 'user';
            if (role === 'admin') {
                window.location.href = '../admin/dashboard.php';
            } else if (role === 'driver') {
                window.location.href = '../driver/dashboard.php';
            } else {
                window.location.href = 'profile.php';
            }
        } catch (e) {
            console.error(e);
            showError('Network error. Please try again.');
        }
    }
</script>

</body>
</html>