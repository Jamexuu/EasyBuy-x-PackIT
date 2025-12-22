<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PackIT - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

    <style>
        body {
            background-color: #fffef5;
            /* Matches your index background */
        }

        .btn-brand {
            background-color: #facc15;
            color: #212529;
            font-weight: 600;
            border: none;
        }

        .btn-brand:hover {
            background-color: #eab308;
            color: #000;
        }

        .form-control:focus {
            border-color: #facc15;
            box-shadow: 0 0 0 0.25rem rgba(250, 204, 21, 0.25);
        }
    </style>
</head>

<body class="d-flex align-items-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">

                <div class="card border-0 shadow-lg rounded-4 p-4">
                    <div class="card-body">

                        <div class="text-center mb-4">
                            <h1 class="h3 fw-bold mb-1">Welcome Back</h1>
                            <p class="text-muted small">Please enter your details to sign in.</p>
                        </div>

                        <form action="#" method="POST">

                            <div class="mb-3">
                                <label for="email" class="form-label small fw-bold">Email address</label>
                                <input type="email" class="form-control bg-light" name="email" id="email"
                                    placeholder="name@example.com" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label small fw-bold">Password</label>
                                <input type="password" class="form-control bg-light" name="password" id="password"
                                    placeholder="Enter your password" required>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rememberMe" id="rememberMe">
                                    <label class="form-check-label small" for="rememberMe">
                                        Remember me
                                    </label>
                                </div>
                                <a href="#" class="text-decoration-none text-dark fw-bold small">Forgot password?</a>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-brand btn-lg rounded-pill">Sign In</button>
                            </div>

                        </form>

                        <div class="position-relative my-4">
                            <hr class="text-secondary opacity-25">
                            <span
                                class="position-absolute top-50 start-50 translate-middle bg-white px-2 text-muted small"
                                style="font-size: 12px;">OR CONTINUE WITH</span>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="button"
                                class="btn btn-outline-dark rounded-pill d-flex align-items-center justify-content-center gap-2 py-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                    class="bi bi-bag-fill text-success" viewBox="0 0 16 16">
                                    <path
                                        d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5z" />
                                </svg>
                                EasyBuy Account
                            </button>
                        </div>

                        <div class="text-center mt-4">
                            <p class="mb-0 small text-muted">Don't have an account? <a href="signUp.php"
                                    class="text-dark fw-bold text-decoration-underline">Sign up</a></p>
                        </div>

                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="../index.php" class="text-decoration-none text-muted small">&larr; Back to Home</a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>