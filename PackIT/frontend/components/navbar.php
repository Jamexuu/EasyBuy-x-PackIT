<?php
$base_path = file_exists('assets/LOGO.svg') ? '' : '../';
?>
<div class="container sticky-top mt-3" style="z-index: 1030;">
    <nav class="navbar navbar-expand-lg rounded-pill shadow px-3 py-2 mx-auto"
         style="background-color: #f8e15b; max-width: 95%;">
        <div class="container-fluid">

            <a class="navbar-brand d-flex align-items-center" href="<?php echo $base_path; ?>index.php">
                <img src="<?php echo $base_path; ?>assets/LOGO.svg" alt="PackIT" height="40" class="object-fit-contain">
                <span class="ms-2 fw-bold text-dark" style="font-size: 1.25rem;">PackIT</span>
            </a>

            <button class="navbar-toggler border-0 p-0 d-lg-none" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                    aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="d-none d-lg-flex align-items-center w-100">
                <ul class="navbar-nav gap-3 text-center d-flex justify-content-center flex-grow-1">
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-bold text-uppercase px-3" href="#">Payment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-bold text-uppercase px-3" href="#">Transactions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-bold text-uppercase px-3" href="#">Vehicles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-bold text-uppercase px-3" href="#">Records</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3 justify-content-end">
                    <button class="btn p-0 border-0 text-dark" aria-label="Notifications">
                        <i class="bi bi-bell fs-4"></i>
                    </button>
                    <a href="<?php echo $base_path; ?>frontend/login.html"
                       class="text-dark text-decoration-none fw-bold text-uppercase lh-1"
                       style="font-size: 0.9rem;">
                        Login/<br>Signup
                    </a>
                </div>
            </div>

            <div class="d-lg-none offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" style="background-color: #f8e15b;"
                 aria-labelledby="offcanvasNavbarLabel" data-bs-scroll="false" data-bs-backdrop="true">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">
                        <img src="<?php echo $base_path; ?>assets/LOGO.svg" alt="PackIT" height="32" class="me-2 align-middle">
                        Menu
                    </h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-bold text-uppercase" href="#">Payment</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-bold text-uppercase" href="#">Transactions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-bold text-uppercase" href="#">Vehicles</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-bold text-uppercase" href="#">Records</a>
                        </li>
                    </ul>

                    <div class="mt-3 border-top pt-3">
                        <a href="<?php echo $base_path; ?>frontend/login.html"
                           class="nav-link text-dark fw-bold text-uppercase">
                            Login / Signup
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </nav>
</div>