<?php
require_once '../api/classes/Auth.php';
require_once '../api/classes/User.php';

Auth::redirectIfLoggedIn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['Email'];
    $password = $_POST['Password'];

    $user = new User();
    $result = $user->login($email, $password);

    if ($result) {
        Auth::login($result['id'], $result['email'], $result['first_name']);
        header("Location: ../index.php");
        exit();
    } else {
        header("Location: login.php?error=invalid_credentials");
        exit();
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EasyBuy - Login or Sign Up</title>
    <link rel="shortcut icon" href="../assets/easybuylogo.svg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col">
                <div class="p-5 gradient-banner" style="background: var(--gradient-color);"></div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col d-flex justify-content-center align-items-center">
                <img src="../assets/easybuylogo.svg" alt="Badge"
                    style="height: 370px; width: 370px; object-fit: cover;">
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-xl-6">
                <form method="POST" action="">
                    <!--Modal error message-->
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_credentials'): ?>
                        <div id="errorModal" class="modal fade" tabindex="-1" aria-labelledby="errorModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content p-4 rounded-5 shadow text-center border-0"
                                    style="width: 85%; max-width: 320px; margin: auto;">
                                    <div class="h5 fw-bold mb-2">Error</div>
                                    <p class="mb-4"> Invalid Credentials</p>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="btn px-4 text-white" style="background-color: #6EC064;" data-bs-dismiss="modal">
                                            OK
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label" for="Email">Email</label>
                        <input type="email" id="Email" placeholder="Email" name="Email"
                            class="form-control p-2 rounded-3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label rounded-3" for="Password">Password</label>
                        <input type="password" id="Password" placeholder="Password" name="Password"
                            class="form-control p-2">
                    </div>
                    <div class="form-check mb-3 ms-1">
                        <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe">
                        <label class="form-check-label small text-muted" for="rememberMe">Remember me</label>
                    </div>

                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn text-white px-4 py-2 fw-bold w-100 rounded-3"
                            style="background-color: #6EC064">Log in</button>
                    </div>
                    <div class="mt-2 d-flex justify-content-center">
                        <p class="text-muted">or log in using</p>
                    </div>
                    <div class="d-flex justify-content-center">
                        <!--This is where the endpoint of PACKIT-->
                        <a href="" class="btn w-100 d-flex align-items-center justify-content-center gap-2 rounded-3"
                            style="background-color: #F8E15B;">
                            <!--This is where the logo of PACKIT-->
                            <img src="../../PackIT/assets/LOGO.svg" width="30" height="30" alt="logo of packit">
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
    <script>
        const modal = new bootstrap.Modal(document.getElementById('errorModal'));
        modal.show();
    </script>
</body>

</html>