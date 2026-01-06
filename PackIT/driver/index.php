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
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .driver-card {
            border-top: 5px solid #ffc107; /* Distinct yellow top border */
        }
    </style>
</head>

<body class="d-flex align-items-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                
                <div class="card driver-card border-0 shadow-sm rounded-3 p-4 bg-white">
                    <div class="card-body">
                        
                        <div class="mb-4 text-center">
                            <h5 class="text-uppercase text-muted fw-bold small ls-1 mb-1">PackIT Services</h5>
                            <h1 class="h3 fw-bold mb-0">Driver Login</h1>
                        </div>

                        <?php if (! empty($_SESSION['success'])) : ?>
                            <div class="alert alert-success small py-2 text-center"><?= htmlspecialchars($_SESSION['success']) ?></div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>

                        <?php if (!empty($error)) : ?>
                            <div class="alert alert-danger small py-2 text-center">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-bold">Driver Email</label>
                                <input type="email" class="form-control bg-light" name="email" id="email"
                                    placeholder="name@driver.packit.com" required>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label small fw-bold">Password</label>
                                <input type="password" class="form-control bg-light" name="password" id="password"
                                    placeholder="Enter your password" required>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-warning rounded-pill fw-semibold">
                                    Sign In
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="small text-muted mb-0">Not a driver?</p>
                            <a href="signup.php" class="text-dark fw-bold text-decoration-none small">Apply Here</a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="../index.php" class="text-decoration-none text-muted small">
                        &larr; Back to Home
                    </a>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>