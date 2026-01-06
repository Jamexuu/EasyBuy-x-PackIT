<?php
session_start();

// Include Auth and User classes
require_once __DIR__ .'/../api/classes/Auth.php';
require_once __DIR__ .'/../api/classes/User.php';

// Require authentication
Auth::requireAuth();

// Get user details from database
$userObj = new User();
$user = $userObj->getUserDetails($_SESSION['user_id']);

if (!$user) {
    Auth::logout();
    header('Location: login.php');
    exit();
}
?>
<! doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PackIT - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #fffef5;">
<div class="container py-5">
    <div class="card shadow-lg rounded-4 p-4 mx-auto" style="max-width: 600px;">
        <h2 class="text-center fw-bold mb-4">Welcome, <?= htmlspecialchars($user['first_name']) ?>!</h2>
        
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Name:</strong> <?= htmlspecialchars($user['first_name'] .' ' .$user['last_name']) ?></li>
            <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></li>
            <li class="list-group-item"><strong>Contact:</strong> <?= htmlspecialchars($user['contact_number']) ?></li>
            <li class="list-group-item"><strong>Address:</strong> 
                <?= htmlspecialchars($user['house_number'] .', ' .$user['street']) ?>,
                <?= htmlspecialchars($user['subdivision'] ??  '') ?>,
                <?= htmlspecialchars($user['barangay']) ?>,
                <?= htmlspecialchars($user['city']) ?>,
                <?= htmlspecialchars($user['province']) ?>,
                <?= htmlspecialchars($user['postal_code']) ?>
            </li>
            <li class="list-group-item"><strong>Member Since:</strong> <?= htmlspecialchars($user['created_at']) ?></li>
        </ul>

        <div class="text-center mt-4">
            <a href="booking/package.php" class="btn btn-warning rounded-pill me-2">Book a Delivery</a>
            <a href="logout.php" class="btn btn-danger rounded-pill">Logout</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>