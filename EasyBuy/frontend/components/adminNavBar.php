<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="/EasyBuy-x-PackIT/EasyBuy/assets/css/style.css">
</head>

<body>
    <div class="navbar navbar-expand-lg" style="background: var(--gradient-color-adminNav);">
        <div class="container-fluid">
            <a class="navbar-brand" href="/EasyBuy-x-PackIT/EasyBuy/admin/adminDashboard.php">
                <img src="/EasyBuy-x-PackIT/EasyBuy/assets/navbar_logo.svg" alt="EasyBuy" class="img-fluid px-lg-3 p-2 ms-5"
                    style="max-height: 60px;">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto me-5 gap-5">
                    <li class="nav-item">
                        <a class="nav-link text-white fw-normal" href="adminDashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white fw-normal" href="adminProducts.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white fw-normal" href="adminOrders.php">Orders</a>
                    </li>
                    <li class="nav-item position-relative">
                        <a class="nav-link text-white fw-normal position-relative" href="adminEmail.php">
                            Email
                            <span id="unreadEmailsBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.8rem; display:none;">0</span>
                        </a>
                    </li>
                    <li class="nav-item position-relative">
                        <a class="nav-link text-white fw-normal position-relative" href="adminSMS.php">
                            SMS
                            <span id="unreadMessagesBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.8rem; display:none;">0</span>
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
                if (emailBadge) {
                    emailBadge.textContent = unreadEmails;
                    emailBadge.style.display = unreadEmails > 0 ? 'inline-block' : 'none';
                }
                if (smsBadge) {
                    smsBadge.textContent = unreadMessages;
                    smsBadge.style.display = unreadMessages > 0 ? 'inline-block' : 'none';
                }
            } catch (e) {}
        }
        document.addEventListener('DOMContentLoaded', updateAdminNavBadges);
        setInterval(updateAdminNavBadges, 30000);
    </script>
</body>

</html>