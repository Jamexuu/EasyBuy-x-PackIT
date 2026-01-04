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
        $stmt = $db->executeQuery("SELECT id, password FROM drivers WHERE email = ? LIMIT 1", [$email]);
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
</head>

<body class="d-flex align-items-center min-vh-100" style="background-color:  #fffef5;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card border-0 shadow-lg rounded-4 p-4">
                    <div class="card-body">
                        <h1 class="h3 fw-bold mb-3 text-center">Welcome Back</h1>

                        <?php if (! empty($_SESSION['success'])) : ?>
                            <div class="alert alert-success small"><?= htmlspecialchars($_SESSION['success']) ?></div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>

                        <?php if (!empty($error)) : ?>
                            <div class="alert alert-danger small"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-bold">Email address</label>
                                <input type="email" class="form-control bg-light" name="email" id="email"
                                    placeholder="driver@example.com" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label small fw-bold">Password</label>
                                <input type="password" class="form-control bg-light" name="password" id="password"
                                    placeholder="Enter your password" required>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-warning btn-lg rounded-pill">Sign In</button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <p class="small text-muted">Don't have an account?
                                <a href="signup.php" class="text-dark fw-bold">Sign up</a>
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
</body>

</html>