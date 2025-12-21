<?php
$base_path = file_exists('assets/LOGO.svg') ? '' : '../';
?>

<div class="container">
    <nav class="navbar navbar-expand-lg my-3 mx-auto rounded-pill shadow px-4 py-2"
        style="background-color: #f8e15b; max-width: 95%;">
        <div class="container-fluid">

            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="<?php echo $base_path; ?>assets/LOGO.svg" alt="PackIT" height="40" class="object-fit-contain">
            </a>

            <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav gap-3 text-center my-3 my-lg-0">
                    <li class="nav-item"><a class="nav-link text-dark fw-bold text-uppercase px-3" href="#">Payment</a>
                    </li>
                    <li class="nav-item"><a class="nav-link text-dark fw-bold text-uppercase px-3"
                            href="#">Transactions</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-bold text-uppercase px-3" href="#">Vehicles</a>
                    </li>
                    <li class="nav-item"><a class="nav-link text-dark fw-bold text-uppercase px-3" href="#">Records</a>
                    </li>
                </ul>
            </div>

            <div class="d-none d-lg-flex align-items-center gap-3">
                <button class="btn p-0 border-0 text-dark"><i class="bi bi-bell fs-4"></i></button>
                <a href="<?php echo $base_path; ?>frontend/login.html"
                    class="text-dark text-decoration-none fw-bold text-uppercase lh-1 small">
                    Login/<br>Signup
                </a>
            </div>
        </div>
    </nav>
</div>