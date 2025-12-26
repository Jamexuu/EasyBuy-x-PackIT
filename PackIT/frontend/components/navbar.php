<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    :root {
      --brand-yellow: #f8e15b;
      --brand-dark: #111;
    }

    .bg-brand { background-color: var(--brand-yellow) !important; }
    
    body { font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }

    .hover-scale { transition: transform 0.2s ease-in-out; }
    .hover-scale:hover { transform: scale(1.15); }

    .footer-curve {
      height: 90px;
      background: var(--brand-yellow);
      clip-path: ellipse(85% 100% at 50% 100%);
    }
</style>
<div class="container">
    <nav class="navbar navbar-expand-lg my-3 mx-auto rounded-pill shadow px-4 py-2 bg-brand" style="max-width: 95%;">
        <div class="container-fluid">
            
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="/EasyBuy-x-PackIT/PackIT/assets/LOGO.svg" alt="PackIT" height="40" class="object-fit-contain">
            </a>

            <div class="d-flex align-items-center gap-2 gap-lg-3 order-lg-3">
                <button class="btn p-0 border-0 text-dark"><i class="bi bi-bell fs-4"></i></button>
                <a href="frontend/login.php" class="text-dark text-decoration-none fw-bold text-uppercase lh-1 d-none d-sm-block" style="font-size: 0.8rem;">
                    Login/<br>Signup
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
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-center flex-grow-1 gap-3 text-uppercase small fw-bold">
                        <?php
                        $navItems = [
                            'payment.php' => 'Payment',
                            'transactions.php' => 'Transactions',
                            'vehicles.php' => 'Vehicles',
                            'records.php' => 'Records'
                        ];
                        foreach ($navItems as $url => $label):
                        ?>
                            <li class="nav-item">
                                <a class="nav-link text-dark <?= ($page == $url) ? 'fw-bolder text-decoration-underline' : '' ?>" href="<?= $url ?>">
                                    <?= $label ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

        </div>
    </nav>
</div>