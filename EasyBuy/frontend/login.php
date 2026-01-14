<?php
    require_once '../api/classes/Auth.php';
    require_once '../api/config.php';

    Auth::redirectIfLoggedIn();
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
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

    <div class="container mb-3">
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
                                <button type="button" class="btn px-4 text-white" style="background-color: #6EC064;" data-bs-dismiss="modal">
                                    OK
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-danger small fw-normal" id="errorMessage"></p>
                <form method="POST" action="" onsubmit="postUserData(event); return false;">
                    <div class="mb-3">
                        <label class="form-label" for="Email">Email</label>
                        <input type="email" id="Email" placeholder="Email" name="Email"
                            class="form-control p-2 rounded-3">
                        <p class="text-danger small fw-normal" id="emailError"></p>   
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="Password">Password</label>
                        <div class="input-group">
                            <input type="password" id="Password" placeholder="Password" name="password"
                                class="form-control p-2 border-end-0">
                            <span class="input-group-text bg-white border-start-0" style="cursor: pointer;" id="togglePassword">
                                <span class="material-symbols-outlined text-secondary">
                                    visibility
                                </span>
                            </span>
                        </div>
                        <p class="text-danger small fw-normal" id="passwordError"></p>
                    </div>
                    <div class="small mb-3">
                        <a href="enterEmailForgotPassword.php" style="color: #6EC064" class="text-decoration-none">Forgot Password?</a>
                    </div>

                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn text-white px-4 py-2 fw-bold w-100 rounded-3"
                            style="background-color: #6EC064">Log in</button>
                    </div>
                    <div class="mt-2 d-flex justify-content-center">
                        <p class="text-muted">or</p>
                    </div>
                    <div class="d-flex justify-content-center">
                        <!--This is where the endpoint of PACKIT-->
                        <a href="packitLogin.php" class="btn w-100 d-flex align-items-center justify-content-center gap-2 rounded-3"
                            style="background-color: #F8E15B;">
                            <!--This is where the logo of PACKIT-->
                            <img src="../../PackIT/assets/LOGO.svg" width="30" height="30" alt="logo of packit">
                            Sign in with PackIT
                        </a>
                    </div>
                    <p class="text-center mb-1 mt-2">
                        Don't have an account?
                        <a href="signUp.php" class="text-black text-decoration-underline ">Sign Up with
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
        const errorModalElement = document.getElementById('errorModal');
        const errorMessageElement = document.getElementById('errorMessage');
        const email = document.getElementById('Email');
        const password = document.getElementById('Password');
        const togglePassword = document.getElementById('togglePassword');
        const passwordError = document.getElementById('passwordError');
        const emailError = document.getElementById('emailError');
        const errorMessage = document.getElementById('errorMessage');

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.input-group-text').forEach(function(toggle) {
                toggle.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    const icon = this.querySelector('.material-symbols-outlined');
                    
                    if (input && input.type === 'password') {
                        input.type = 'text';
                        icon.textContent = 'visibility_off';
                    } else if (input) {
                        input.type = 'password';
                        icon.textContent = 'visibility';
                    }
                });
            });
        });
    
        <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_credentials'): ?>
        email.style.border = '1px solid red';
        password.style.border = '1px solid red';
        togglePassword.classList.add('border-danger', 'border-1');
        errorMessageElement.textContent = 'Invalid Credentials';
        const modal = new bootstrap.Modal(errorModalElement);
        errorMessageElement.textContent = 'Invalid Credentials';
        modal.show();
        <?php endif; ?>

        function showErrorModal(message) {
            errorMessageElement.textContent = message;
            const modal = new bootstrap.Modal(errorModalElement);
            modal.show();
        }



        async function postUserData(event) {
            event.preventDefault();

            email.styleborder = '';
            password.style.border = '';
            togglePassword.classList.remove('border-danger', 'border-1');

            if (!email.value || !password.value) {
                showErrorModal('Please fill in all fields.');
                email.style.border = '1px solid red';
                password.style.border = '1px solid red';
                togglePassword.classList.add('border-danger', 'border-1');
                emailError.textContent = !email.value ? 'Please enter your email.' : '';
                passwordError.textContent = !password.value ? 'Please enter your password.' : '';
                email.focus();
                return;
            }

            const payload = {
                email: email.value,
                password: password.value
            }

            try {
                const response = await fetch('../api/login.php', {
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
                    showErrorModal(data.error || 'Login failed. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                showErrorModal('An error occurred. Please try again.');
            }
        }

    </script>
</body>

</html>