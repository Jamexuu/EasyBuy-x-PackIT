<?php
session_start();
require_once __DIR__ . '/../api/classes/Database.php';

$db = new Database();
$error = "";

if (isset($_SESSION['driver_id'])) {
    header("Location: driver.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = "Please enter email and password.";
    } else {
        $stmt = $db->executeQuery(
            "SELECT id, password FROM drivers WHERE email = ? LIMIT 1",
            [$email]
        );
        $rows = $db->fetch($stmt);

        if (empty($rows)) {
            $error = "Invalid email or password.";
        } else {
            $driver = $rows[0];
            if (!password_verify($password, $driver['password'])) {
                $error = "Invalid email or password.";
            } else {
                $_SESSION['driver_id'] = (int)$driver['id'];
                header("Location: driver.php");
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
    <title>PackIT - Driver Login</title>

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
                        <i class="bi bi-person-badge-fill"></i>
                    </div>

                    <h1 class="h3 fw-bold mb-1 text-center">Driver Login</h1>
                    <p class="text-muted small text-center mb-4">
                        Sign in to your driver dashboard
                    </p>

                    <?php if (!empty($_SESSION['success'])): ?>
                        <div class="alert alert-success small">
                            <?= htmlspecialchars($_SESSION['success']) ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger small">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">

                        <!-- EMAIL -->
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold">
                                Email address
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email"
                                       class="form-control bg-light"
                                       name="email"
                                       id="email"
                                       placeholder="driver@example.com"
                                       required>
                            </div>
                        </div>

                        <!-- PASSWORD WITH EYE ICON -->
                        <div class="mb-3">
                            <label for="password" class="form-label small fw-bold">
                                Password
                            </label>
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
                                        id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit"
                                    class="btn btn-warning btn-lg rounded-pill">
                                Sign In
                            </button>
                        </div>

                    </form>

                    <div class="text-center mt-3">
                        <p class="small text-muted mb-0">
                            Don't have a driver account?
                            <a href="signup.php" class="text-dark fw-bold">
                                Sign up
                            </a>
                        </p>
                    </div>

                </div>
            </div>

            <div class="text-center mt-4">
                <a href="../index.php"
                   class="text-decoration-none text-muted small">
                    &larr; Back to Home
                </a>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const icon = togglePassword.querySelector('i');

    togglePassword.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';

        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    });
</script>

</body>
</html>
