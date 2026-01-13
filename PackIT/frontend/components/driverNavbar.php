<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../api/classes/Database.php';

$driverId = isset($_SESSION['driver_id']) ? (int)$_SESSION['driver_id'] : 0;
$driverName = null;

if ($driverId > 0) {
    $db = new Database();
    $stmt = $db->executeQuery("SELECT first_name, last_name FROM drivers WHERE id = ? LIMIT 1", [$driverId]);
    $rows = $db->fetch($stmt);
    if (!empty($rows)) $driverName = trim(($rows[0]['first_name'] ?? '') . ' ' . ($rows[0]['last_name'] ?? ''));
}

$homeUrl = "driver.php";
$bookingsUrl = "driverBookings.php";
$transactionsUrl = "driverTransactions.php";
$profileUrl = "driverProfile.php";
?>
<style>
    :root { --brand-yellow: #f8e15b; --brand-dark: #111; }
    .bg-brand { background-color: var(--brand-yellow) !important; }
    .navbar-pill { border-radius: 50px; max-width: 95%; }
    .nav-link { color: var(--brand-dark) !important; font-weight: 800; text-transform: uppercase; font-size: 0.85rem; }
    
    .driver-name-link { 
        font-weight: 800; 
        text-transform: uppercase; 
        font-size: 0.85rem; 
        color: var(--brand-dark) !important; 
        text-decoration: none;
    }

    /* Prevents the name from breaking the layout on very small screens */
    .driver-name-wrapper {
        max-width: 120px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media (min-width: 992px) {
        .driver-name-wrapper { max-width: 200px; }
    }
</style>

<div class="container">
    <nav class="navbar navbar-expand-lg my-3 mx-auto shadow px-4 py-2 bg-brand navbar-pill">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo htmlspecialchars($homeUrl); ?>">
                <img src="/EasyBuy-x-PackIT/PackIT/assets/LOGO.svg" alt="PackIT" height="40" class="object-fit-contain">
            </a>

            <div class="d-flex align-items-center order-lg-3 ms-auto">
                <div class="driver-name-wrapper me-2 me-lg-0">
                    <?php if ($driverName): ?>
                        <a class="driver-name-link" href="<?php echo htmlspecialchars($profileUrl); ?>">
                            <?php echo htmlspecialchars($driverName); ?>
                        </a>
                    <?php else: ?>
                        <a class="driver-name-link" href="<?php echo htmlspecialchars($profileUrl); ?>">Profile</a>
                    <?php endif; ?>
                </div>

                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#driverNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse justify-content-center order-lg-2" id="driverNavbar">
                <ul class="navbar-nav gap-lg-4 text-center mt-3 mt-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars($homeUrl); ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars($bookingsUrl); ?>">Bookings</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars($transactionsUrl); ?>">Transactions</a></li>
                </ul>
            </div>
        </div>
    </nav>
</div>