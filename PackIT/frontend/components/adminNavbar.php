<!-- <!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PackIT Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --brand-yellow: #f8e15b;
            --brand-dark: #111;
            --border-gray: #dee2e6;
        }

        body {
            background-color: #f8f9fa;
        }

        .bg-brand {
            background-color: var(--brand-yellow) !important;
        }

        /* --- Admin Sidebar Styling --- */
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

        .admin-nav-card.active:hover {
            background-color: var(--brand-yellow);
            border-color: var(--brand-yellow);
        }

        /* Main Content Area */
        .content-area {
            background-color: white;
            border-radius: 1rem;
            border: 1px solid var(--border-gray);
            min-height: 80vh;
        }
    </style>
</head>

<body>

    <div class="container">
        <nav class="navbar navbar-expand-lg my-3 mx-auto rounded-pill shadow px-4 py-2 bg-brand" style="max-width: 95%;">
            <div class="container-fluid">

                <a class="navbar-brand d-flex align-items-center gap-2" href="../index.php">
                    <img src="/EasyBuy-x-PackIT/PackIT/assets/LOGO.svg" alt="PackIT" height="40" class="object-fit-contain">
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

            </div>
        </nav>
    </div>

    <div class="container pb-5">
        <div class="row g-4">

            <div class="col-lg-3 col-md-4">
                <div class="d-grid gap-3" id="sidebarMenu">
                    <a href="#" class="admin-nav-card shadow-sm" onclick="setActive(this)">Users</a>
                    <a href="#" class="admin-nav-card shadow-sm" onclick="setActive(this)">Address</a>
                    <a href="#" class="admin-nav-card shadow-sm" onclick="setActive(this)">Drivers</a>
                    <a href="#" class="admin-nav-card shadow-sm" onclick="setActive(this)">Vehicles</a>
                    <a href="#" class="admin-nav-card shadow-sm" onclick="setActive(this)">Payments</a>
                    <a href="#" class="admin-nav-card shadow-sm" onclick="setActive(this)">Bookings</a>
                </div>
            </div>

            <div class="col-lg-9 col-md-8">
                <div class="content-area shadow-sm p-5">
                    <h4 class="fw-bold mb-4" id="pageTitle">Dashboard</h4>
                    <p class="text-muted">Select an item from the menu to view details.</p>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function setActive(element) {
            const links = document.querySelectorAll('.admin-nav-card');
            links.forEach(link => {
                link.classList.remove('active');
            });
            element.classList.add('active');
            document.getElementById('pageTitle').innerText = element.innerText;
        }
    </script>
</body>

</html> -->