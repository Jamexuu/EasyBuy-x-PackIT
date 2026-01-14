<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="/EasyBuy-x-PackIT/EasyBuy/assets/css/style.css">
</head>

<body>
    
    <div class="container-fluid p-3" style="background: var(--gradient-color-adminNav);">
        <div class="row align-items-center">
            <div class="col-6 px-3 d-none d-md-block">
                <a href="/EasyBuy-x-PackIT/EasyBuy/admin/adminDashboard.php" class="text-decoration-none">
                    <img src="../assets/navbar_logo.svg" alt="easybuy logo" class="img-fluid" style="height: 70px;">
                </a>
            </div>
            <div class="col col-md-6">
                <ul class="d-none d-md-flex list-unstyled ms-auto justify-content-end gap-4">
                    <li>
                        <a class="nav-link" href="adminDashboard.php">Dashboard</a>
                    </li>
                    <li>
                        <a class="nav-link" href="adminProducts.php">Products</a>
                    </li>
                    <li>
                        <a class="nav-link" href="adminOrders.php">Orders</a>
                    </li>
                    <li class="position-relative">
                        <a class="nav-link position-relative" href="adminEmail.php">
                            Email
                            <span id="unreadEmailsBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.8rem; display:none;">0</span>
                        </a>
                    </li>
                    <li class="">
                        <a class="nav-link position-relative" href="adminSMS.php">
                            SMS
                            <span id="unreadMessagesBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.8rem; display:none;">0</span>
                        </a>
                    </li>
                </ul>
                <ul class="d-flex d-md-none list-unstyled ms-auto justify-content-around gap-4">
                    <li>
                        <a class="nav-link" href="adminDashboard.php">
                            <span class="material-symbols-outlined">
                                dashboard
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="adminProducts.php">
                            <span class="material-symbols-outlined">
                                grocery
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="adminOrders.php">
                            <span class="material-symbols-outlined">
                                receipt_long
                            </span>
                        </a>
                    </li>
                    <li class="position-relative">
                        <a class="nav-link position-relative" href="adminEmail.php">
                            <span class="material-symbols-outlined">
                                mail
                            </span>
                            <span id="unreadMobileEmailsBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.8rem; display:none;">0</span>
                        </a>
                    </li>
                    <li class="">
                        <a class="nav-link position-relative" href="adminSMS.php">
                            <span class="material-symbols-outlined">
                                sms
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script>
        async function updateAdminNavBadges() {
            try {
                const response = await fetch('/EasyBuy-x-PackIT/EasyBuy/api/getAdminDashboardStats.php');
                const data = await response.json();
                const unreadEmails = data.unreadEmails || 0;
                const unreadMessages = data.unreadMessages || 0;
                const emailBadge = document.getElementById('unreadEmailsBadge');
                const smsBadge = document.getElementById('unreadMessagesBadge');
                const emailBadgeMobile = document.getElementById('unreadMobileEmailsBadge');

                if (emailBadge) {
                    emailBadge.textContent = unreadEmails;
                    emailBadge.style.display = unreadEmails > 0 ? 'inline-block' : 'none';
                }
                if (emailBadgeMobile) {
                    emailBadgeMobile.textContent = unreadEmails;
                    emailBadgeMobile.style.display = unreadEmails > 0 ? 'inline-block' : 'none';
                }
            } catch (e) {}
        }
        document.addEventListener('DOMContentLoaded', updateAdminNavBadges);
        setInterval(updateAdminNavBadges, 30000);
    </script>
</body>

</html>