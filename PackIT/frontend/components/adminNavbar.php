<?php
$activePage = $activePage ?? 'dashboard';
$basePath   = $basePath ?? '../';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    :root {
        --brand-yellow: #f8e15b;
        --brand-dark: #111;
        --border-gray: #dee2e6;
    }

    body { background-color: #f8f9fa; }
    .bg-brand { background-color: var(--brand-yellow) !important; }

    .admin-nav-card {
        display: block;
        width: 100%;
        padding: 1.5rem 1rem;
        text-align: center;
        text-decoration: none;
        color: var(--brand-dark);
        background-color: white;
        border: 2px solid var(--border-gray);
        border-radius: 1rem;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }

    .admin-nav-card:hover {
        border-color: var(--brand-yellow);
        transform: translateY(-2px);
        color: var(--brand-dark);
        background-color: white;
    }

    .admin-nav-card.active {
        background-color: var(--brand-yellow);
        border-color: var(--brand-yellow);
        font-weight: bold;
    }

    .content-area {
        background-color: white;
        border-radius: 1rem;
        border: 1px solid var(--border-gray);
        min-height: 80vh;
    }
</style>

<div class="container">
    <nav class="navbar navbar-expand-lg my-3 mx-auto rounded-pill shadow px-4 py-2 bg-brand" style="max-width: 95%;">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
                <img src="<?= htmlspecialchars($basePath) ?>assets/LOGO.svg" alt="PackIT" height="40" class="object-fit-contain">
                <span class="fw-bold">PackIT Admin</span>
            </a>

            <a href="logout.php" class="text-dark text-decoration-none fw-bold text-uppercase lh-1 d-none d-lg-block" style="font-size: 0.9rem;">
                Logout <i class="bi bi-box-arrow-right ms-1"></i>
            </a>

            <button class="navbar-toggler border-0 p-0 ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="offcanvas offcanvas-end rounded-start-5" tabindex="-1" id="offcanvasNavbar">
            <div class="offcanvas-header bg-brand">
                <h5 class="offcanvas-title fw-bold">MENU</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column justify-content-center align-items-center">
                <a href="logout.php" class="btn btn-dark w-100 rounded-pill text-uppercase fw-bold d-lg-none mt-auto mb-3">
                    Logout
                </a>
            </div>
        </div>
    </nav>
</div>

<div class="container pb-5">
    <div class="row g-4">
        <div class="col-lg-3 col-md-4">
            <div class="d-grid gap-3" id="sidebarMenu">
                <a href="dashboard.php" class="admin-nav-card shadow-sm <?= $activePage === 'dashboard' ? 'active' : '' ?>">
                    Dashboard
                </a>

                <a href="dbTables.php?view=users" class="admin-nav-card shadow-sm <?= $activePage === 'users' ? 'active' : '' ?>">
                    Users
                </a>
                <a href="dbTables.php?view=addresses" class="admin-nav-card shadow-sm <?= $activePage === 'addresses' ? 'active' : '' ?>">
                    Addresses
                </a>
                <a href="dbTables.php?view=drivers" class="admin-nav-card shadow-sm <?= $activePage === 'drivers' ? 'active' : '' ?>">
                    Drivers
                </a>

                <a href="vehicles.php" class="admin-nav-card shadow-sm <?= $activePage === 'vehicles' ? 'active' : '' ?>">
                    Vehicles
                </a>

                <a href="dbTables.php?view=driver_vehicles" class="admin-nav-card shadow-sm <?= $activePage === 'driver_vehicles' ? 'active' : '' ?>">
                    Driver Vehicles
                </a>

                <a href="dbTables.php?view=payments" class="admin-nav-card shadow-sm <?= $activePage === 'payments' ? 'active' : '' ?>">
                    Payments
                </a>
                <a href="dbTables.php?view=bookings" class="admin-nav-card shadow-sm <?= $activePage === 'bookings' ? 'active' : '' ?>">
                    Bookings
                </a>
                
                <a href="dbTables.php?view=smslogs" class="admin-nav-card shadow-sm <?= $activePage === 'smslogs' ? 'active' : '' ?>">
                    SMS Logs
                </a>

                <a href="dbTables.php?view=password_resets" class="admin-nav-card shadow-sm <?= $activePage === 'password_resets' ? 'active' : '' ?>">
                    Password Resets
                </a>

                <a href="dbTables.php?view=chat_history" class="admin-nav-card shadow-sm <?= $activePage === 'chat_history' ? 'active' : '' ?>">
                    Chat History
                </a>
            </div>
        </div>