<?php
session_start();

// Include the User and Auth classes
require_once __DIR__ .'/../api/classes/User.php';
require_once __DIR__ .'/../api/classes/Auth.php';

// Redirect if already logged in
Auth::redirectIfLoggedIn();

$error = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Initialize User class
        $userObj = new User();
        
        // Attempt login (returns user data or false)
        $user = $userObj->login($email, $password);

        if ($user) {
            // Login successful - set session using Auth class
            $fullName = $user['first_name'] .' ' .$user['last_name'];
            Auth::login($user['id'], $user['email'], $fullName, $user['role']);

            // Store user details in session
            $_SESSION['user'] = [
                'id'          => $user['id'],
                'firstName'   => $user['first_name'],
                'lastName'    => $user['last_name'],
                'email'       => $user['email'],
                'contact'     => $user['contact_number'],
                'role'        => $user['role'],
                'created_at'  => $user['created_at']
            ];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: ../admin/dashboard.php');
            } elseif ($user['role'] === 'driver') {
                header('Location:  ../driver/dashboard.php');
            } else {
                header('Location: dashboard.php');
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
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