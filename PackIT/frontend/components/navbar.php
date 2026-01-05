<?php
// Frontend navbar for PackIT
// Place at: frontend/components/navbar.php

// Base URL for your project on localhost — update if your folder/location changes
$BASE_URL = '/EasyBuy-x-PackIT/PackIT';

// Current page filename (used to mark active link)
$page = basename($_SERVER['PHP_SELF']);

// Helper to build full URL
function u($path)
{
    global $BASE_URL;
    return rtrim($BASE_URL, '/') . '/' . ltrim($path, '/');
}

// Simple user detection (set by your login logic)
$loggedIn = isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
$userName = $loggedIn ? trim(($_SESSION['user']['firstName'] ?? '') . ' ' . ($_SESSION['user']['lastName'] ?? '')) : '';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    :root {
        --brand-yellow: #f8e15b;
        --brand-dark: #111;
    }

    .bg-brand {
        background-color: var(--brand-yellow) !important;
    }
</style>

<div class="container">
    <nav class="navbar navbar-expand-lg my-3 mx-auto rounded-pill shadow px-4 py-2 bg-brand" style="max-width: 95%;">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="<?= htmlspecialchars(u('index.php')) ?>">
                <img src="<?= htmlspecialchars(u('assets/LOGO.svg')) ?>" alt="PackIT" height="40" class="object-fit-contain">
            </a>

            <div class="d-flex align-items-center gap-2 gap-lg-3 order-lg-3">
                <button class="btn p-0 border-0 text-dark" title="Notifications"><i class="bi bi-bell fs-4"></i></button>

                <?php if (! $loggedIn): ?>
                    <a href="<?= htmlspecialchars(u('frontend/login.php')) ?>" class="text-dark text-decoration-none fw-bold text-uppercase lh-1 d-none d-sm-block" style="font-size: 0.8rem;">
                        Login/<br>Signup
                    </a>
                <?php else: ?>
                    <div class="d-none d-sm-flex align-items-center gap-2">
                        <a href="<?= htmlspecialchars(u('frontend/profile.php')) ?>" class="text-dark text-decoration-none fw-bold text-uppercase lh-1" style="font-size:0.85rem;">
                            <?= htmlspecialchars($userName ?: 'Profile') ?>
                        </a>
                        <!-- Logout intentionally not in navbar (visible only on profile page) -->
                    </div>
                <?php endif; ?>

                <button class="navbar-toggler border-0 p-0 ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="offcanvas offcanvas-end rounded-start-5" tabindex="-1" id="offcanvasNavbar">
                <div class="offcanvas-header bg-brand">
                    <h5 class="offcanvas-title fw-bold">MENU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-center flex-grow-1 gap-3 text-uppercase small fw-bold">
                        <?php
                        // nav items (removed Book and Tracking as requested)
                        $navItems = [
                            'index.php' => 'Home',
                            'frontend/vehicle.php' => 'Vehicles',
                            'frontend/payment.php' => 'Payment',
                            'frontend/transactions.php' => 'Transactions',
                            'frontend/records.php' => 'Records'
                        ];

                        foreach ($navItems as $path => $label):
                            $isActive = ($page === basename($path));
                            $href = u($path);
                        ?>
                            <li class="nav-item">
                                <a class="nav-link text-dark <?= $isActive ? 'fw-bolder text-decoration-underline' : '' ?>" href="<?= htmlspecialchars($href) ?>">
                                    <?= htmlspecialchars($label) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>

                        <!-- Note: Sign Up / Profile button removed from the offcanvas per your request.
                             Logout remains available only on profile.php inside the menu-outline area. -->
                    </ul>
                </div>
            </div>

        </div>
    </nav>
</div>