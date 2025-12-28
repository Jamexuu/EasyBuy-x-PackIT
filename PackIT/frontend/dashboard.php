<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Grab user data from session
$user = $_SESSION['user'];
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PackIT Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f8f9fa;">
<div class="container py-5">
    <div class="card shadow-sm rounded-4 p-4">
        <h1 class="mb-3">Welcome, <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?>!</h1>
        <p class="text-muted">Here's your profile info:</p>

        <ul class="list-group list-group-flush mb-3">
            <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></li>
            <li class="list-group-item"><strong>Contact:</strong> <?= htmlspecialchars($user['contact']) ?></li>
            <li class="list-group-item"><strong>Address:</strong> 
                <?= htmlspecialchars($user['houseNumber'] . ', ' . $user['street']) ?>,
                <?= htmlspecialchars($user['subdivision']) ?>,
                <?= htmlspecialchars($user['barangay']) ?>,
                <?= htmlspecialchars($user['city']) ?>,
                <?= htmlspecialchars($user['province']) ?>,
                <?= htmlspecialchars($user['postal']) ?>
            </li>
            <li class="list-group-item"><strong>Member Since:</strong> <?= htmlspecialchars($user['created_at']) ?></li>
        </ul>

        <a href="logout.php" class="btn btn-danger rounded-pill">Logout</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
