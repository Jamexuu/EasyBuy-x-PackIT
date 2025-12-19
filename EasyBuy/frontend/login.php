<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col d-flex justify-content-center align-items-center">
                <img src="../assets/easybuylogo.svg" alt="Badge"
                    style="height: 350px; width: 350px; object-fit: cover;">
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-xl-6">
                <form action="POST" action="">
                    <div class="mb-3">
                        <p class="mb-1 ms-3">Email</p>
                        <input type="text" placeholder="  Email" name="Email" class="form-control rounded-pill p-2">
                    </div>
                    <div class="mb-3">
                        <p class="mb-1 ms-3">Password</p>
                        <input type="password" placeholder="  Password" name="Password"
                            class="form-control rounded-pill p-2">
                    </div>
                    <div class="form-check mb-3 ms-1">
                        <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe" <label
                            class="form-check-label small text-muted" for="rememberMe">Remember me</label>
                    </div>

                    <div class="d-flex justify-content-center">
                        <button type="submit"
                            class="btn text-white px-4 py-2 rounded-pill fw-bold w-100" style="background: var(--gradient-color)">Log in</button>
                    </div>
                    <div class="mt-2 d-flex justify-content-center">
                        <p class="text-muted">or log in using</p>
                    </div>
                    <div class="d-flex justify-content-center">
                        <!--This is where the endpoint of PACKIT-->
                        <a href=""
                            class="btn btn-secondary rounded-pill w-100 d-flex align-items-center justify-content-center gap-2">
                            <!--This is where the logo of PACKIT-->
                            <img src="" width="18" height="18" alt="logo of packit">
                            Sign in with PackIT
                        </a>
                    </div>
                    <p class="text-center mb-1 mt-2">
                        Don't have an account?
                        <a href="signUp.php" class="text-black text-decoration-underline ">Sign Up to
                            EasyBuy</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>