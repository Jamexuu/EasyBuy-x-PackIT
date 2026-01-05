<?php
require_once '../api/classes/Auth.php';
require_once '../api/classes/User.php';

Auth::redirectIfAdminLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['adminEmail'] ?? '';
    $password = $_POST['adminPassword'] ?? '';

    $user = new User();
    $result = $user->login($email, $password, 'admin');

    if ($result) {
        Auth::login($result['id'], $result['email'], $result['first_name'], 'admin');
        header("Location: dashboard.php");
        exit();
    }

    header("Location: index.php?error=invalid_credentials");
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PackIT - Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid m-0 p-0">
        <div class="p-5" style="background: linear-gradient(90deg, rgba(57, 130, 80, 1) 8%, rgba(255, 196, 64, 1) 87%);"></div>
    </div>

    <div class="container py-5">
        <h2 class="text-center mb-4">PackIT Admin</h2>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials'): ?>
            <div class="alert alert-danger">Invalid admin credentials.</div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <form method="POST">
                    <label for="adminEmail" class="form-label">Email</label>
                    <input type="text" class="form-control rounded-3 mb-3" placeholder="Email" name="adminEmail" id="adminEmail" required>

                    <label for="adminPassword" class="form-label">Password</label>
                    <input type="password" class="form-control rounded-3" placeholder="Password" name="adminPassword" id="adminPassword" required>

                    <button type="submit" class="btn mt-3 text-white w-100" style="background-color: #398250;">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>