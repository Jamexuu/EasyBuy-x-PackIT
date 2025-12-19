<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/style.css" />
</head>

<body>
    <nav class="navbar navbar-expand-lg" style="background: var(--gradient-color)">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="../assets/navbar_logo.svg" alt="" class="img-fluid px-lg-5 p-2" style="max-height: 68px;">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="material-symbols-outlined text-white fs-2">menu</span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav navbar-text mb-2 ms-auto mb-lg-0 gap-4">
                    <li class="nav-item">
                        <a class="nav-link text-white ps-3" href="#">HOME</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white ps-3" href="#">SALE</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white ps-3" href="#">CATEGORIES</a>
                    </li>
                </ul>
                <div class="d-flex flex-column flex-lg-row align-items-center gap-2 px-5">
                    <div class="d-flex align-items-center gap-2 w-100 w-lg-auto">
                        <input type="text" placeholder="Search" class="form-control rounded-5">
                        <div class="btn">
                            <span class="material-symbols-outlined text-white fs-2">
                                search
                            </span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center">
                        <div class="btn">
                            <span class="material-symbols-outlined text-white fs-1 fs-lg-2">
                                account_circle
                            </span>
                            <br>
                            <span class="text-white">Login</span>
                        </div>
                        <div class="btn">
                            <span class="material-symbols-outlined text-white fs-1 fs-lg-2">
                                shopping_cart
                            </span>
                            <br>
                            <span class="text-white">Cart</span>
                        </div>
                        <div class="btn">
                            <span class="material-symbols-outlined text-white fs-1 fs-lg-2">
                                support_agent
                            </span>
                            <br>
                            <span class="text-white">Support</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>