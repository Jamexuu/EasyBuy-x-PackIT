<?php
session_start();

// Path to your JSON file
$usersFile = __DIR__ . '/../api/data/users.json';


// Make sure the file exists
if (!file_exists($usersFile)) {
    die("User database not found.");
}

// Read the users data
$users = json_decode(file_get_contents($usersFile), true);

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $found = false;

    // Loop through users to find matching email
    foreach ($users as $user) {
        if (strtolower($user['email']) === strtolower($email)) {
            $found = true;

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password correct, start session
                $_SESSION['user'] = [
                    'firstName'   => $user['firstName'] ?? '',
                    'lastName'    => $user['lastName'] ?? '',
                    'contact'     => $user['contact'] ?? '',
                    'email'       => $user['email'],
                    'houseNumber' => $user['houseNumber'] ?? '',
                    'street'      => $user['street'] ?? '',
                    'subdivision' => $user['subdivision'] ?? '',
                    'landmark'    => $user['landmark'] ?? '',
                    'province'    => $user['province'] ?? '',
                    'city'        => $user['city'] ?? '',
                    'barangay'    => $user['barangay'] ?? '',
                    'postal'      => $user['postal'] ?? '',
                    'created_at'  => $user['created_at'] ?? '',
                ];

                // Optional: Gmail login notification
                // require 'gmail_notify.php'; // Your Gmail API code here

                // Redirect to dashboard or home
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Incorrect password.";
            }
            break;
        }
    }

    if (!$found) {
        $error = "Email not registered.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PackIT - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center min-vh-100" style="background-color: #fffef5;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 p-4">
                <div class="card-body">
                    <h1 class="h3 fw-bold mb-3 text-center">Welcome Back</h1>

                    <?php if (!empty($error)) : ?>
                        <div class="alert alert-danger small"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold">Email address</label>
                            <input type="email" class="form-control bg-light" name="email" id="email"
                                   placeholder="name@example.com" required>
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
</body>
</html>
