<?php
require_once __DIR__ . '/../api/classes/Auth.php';
Auth::redirectIfLoggedIn();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PackIT - Sign in with EasyBuy</title>
    <link rel="shortcut icon" href="../../EasyBuy/assets/easybuylogo.svg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col">
                <div class="p-5" style="background-color: #6EC064;"></div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col d-flex justify-content-center align-items-center">
                <img src="../../EasyBuy/assets/easybuylogo.svg" alt="EasyBuy Logo"
                    style="height: 370px; width: 370px; object-fit: cover;">
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-xl-6">
                <div id="errorModal" class="modal fade" tabindex="-1" aria-labelledby="errorModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content p-4 rounded-5 shadow text-center border-0"
                            style="width: 85%; max-width: 320px; margin: auto;">
                            <div class="h5 fw-bold mb-2">Error</div>
                            <p class="mb-4" id="errorMessage">Invalid Credentials</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn px-4 text-white" style="background-color: #6EC064;" data-bs-dismiss="modal">
                                    OK
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <h3>Sign in with EasyBuy</h3>
                    <p class="text-muted">Enter your EasyBuy credentials to access PackIT</p>
                </div>

                <form method="POST" action="" onsubmit="postUserData(event); return false;">
                    <div class="mb-3">
                        <label class="form-label" for="Email">Email</label>
                        <input type="email" id="Email" placeholder="Email" name="Email"
                            class="form-control p-2 rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label rounded-3" for="Password">Password</label>
                        <input type="password" id="Password" placeholder="Password" name="Password"
                            class="form-control p-2" required>
                    </div>
                    
                    <div class="form-check mb-3 ms-1">
                        <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe">
                        <label class="form-check-label small text-muted" for="rememberMe">Remember me</label>
                    </div>

                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn text-white px-4 py-2 fw-bold w-100 rounded-3"
                            style="background-color: #6EC064">Sign in with EasyBuy</button>
                    </div>

                    <p class="text-center mb-1 mt-4">
                        Don't have an EasyBuy account?
                        <a href="../../EasyBuy/frontend/signUp.php" class="text-black text-decoration-underline">
                            Sign Up with EasyBuy
                        </a>
                    </p>
                    
                    <p class="text-center mt-2">
                        <a href="login.php" class="text-muted small text-decoration-none">
                            &larr; Back to PackIT Login
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script>
        const errorModalElement = document.getElementById('errorModal');
        const errorMessageElement = document.getElementById('errorMessage');

        function showErrorModal(message) {
            errorMessageElement.textContent = message;
            const modal = new bootstrap.Modal(errorModalElement);
            modal.show();
        }

        async function postUserData(event) {
            event.preventDefault();

            const payload = {
                email: document.getElementById('Email').value,
                password: document.getElementById('Password').value
            }

            try {
                // Determine base URL dynamically if needed, or stick to relative path
                const response = await fetch('../api/loginWithEasyBuy.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    window.location.href = '../index.php';
                } else {
                    showErrorModal(data.error || 'Login failed. Please check your EasyBuy credentials.');
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorModal('An error occurred. Please try again.');
            }
        }
    </script>
</body>

</html>