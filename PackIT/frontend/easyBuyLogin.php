<?php
require_once __DIR__ . '/../api/classes/Auth.php';
Auth::redirectIfLoggedIn();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EasyBuy - Sign in with EasyBuy</title>
    <link rel="shortcut icon" href="../assets/easybuylogo.svg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
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
                <img src="../../PackIT/assets/easybuylogo.svg" alt="EasyBuy Logo"
                    style="height: 200px; width: 200px; object-fit: cover;">
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-xl-6">
                <!--Modal error message-->
                <div id="errorModal" class="modal fade" tabindex="-1" aria-labelledby="errorModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content p-4 rounded-5 shadow text-center border-0"
                            style="width: 85%; max-width: 320px; margin: auto;">
                            <div class="h5 fw-bold mb-2">Error</div>
                            <p class="mb-4" id="errorMessage">Invalid Credentials</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn px-4 text-white" style="background-color: #F8E15B;" data-bs-dismiss="modal">
                                    OK
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <h3>Sign in with EasyBuy Account</h3>
                    <p class="text-muted">Use your EasyBuy credentials to access EasyBuy</p>
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

                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn text-white px-4 py-2 fw-bold w-100 rounded-3"
                            style="background-color: #F8E15B">Sign in with EasyBuy</button>
                    </div>
                    
                    <p class="text-center mb-1 mt-3">
                        <a href="login.php" class="text-black text-decoration-underline">Back to EasyBuy Login</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
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
                // NOTE: This posts to your existing login endpoint to reuse your current auth logic.
                // If you'd prefer a separate endpoint (e.g. ../api/loginWithPackIT.php), I can add it.
                const response = await fetch('../api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload),
                    credentials: 'same-origin'
                });
                
                const data = await response.json().catch(() => ({}));
                
                if (response.ok && data.success) {
                    // On success, redirect to EasyBuy home or the location you want.
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