<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pack IT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    
</body>

<style>
    :root {
        --brand-yellow: #f8e15b;
        --brand-dark: #111;
    }

    .bg-brand {
        background-color: var(--brand-yellow) !important;
    }

    /* Custom pill shape to match your image */
    .navbar-pill {
        border-radius: 50px;
        max-width: 95%;
    }

    /* Keep links horizontal and bold */
    .nav-link {
        color: var(--brand-dark) !important;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.85rem;
    }
</style>

<div class="container">
    <nav class="navbar navbar-expand-lg my-3 mx-auto shadow px-4 py-2 bg-brand navbar-pill">
        <div class="container-fluid">

            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="/EasyBuy-x-PackIT/PackIT/assets/LOGO.svg" alt="PackIT" height="40" class="object-fit-contain">
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#driverNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="driverNavbar">
                <ul class="navbar-nav gap-lg-4">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="bookings.php">Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                </ul>
            </div>

            <div class="d-none d-lg-flex align-items-center gap-3">
                <button class="btn p-0 border-0">
                <a href="driver_profile.php" class="text-dark text-decoration-none d-flex align-items-center gap-2">
                    <span class="fw-bold text-uppercase small">Profile</span>
                    <i class="bi bi-person-circle fs-4"></i>
                </a>
            </div>

        </div>
    </nav>
</div>