<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EasyBuy - Enter Email for Password Reset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
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
        <div class="row">
            <div class="col text-center">
                <div class="h1">
                    Reset Password
                </div>
                <p>Please enter your new password below. Remember this time!</p>
            </div>
        </div>
        <div class="row mt-3 mb-5">
            <div class="col-12 col-md-8 col-lg-6 mx-auto ">
                <div class="mb-3">
                    <label for="newPassword" class="form-label fw-bold">
                        Password <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="password" id="newPassword" placeholder="Enter Password" name="newPassword"
                            class="form-control p-2 border-end-0" required>
                        <span class="input-group-text bg-white border-start-0" style="cursor: pointer;" id="toggleNewPassword">
                            <span class="material-symbols-outlined text-secondary">
                                visibility
                            </span>
                        </span>
                    </div>
                    <div class="small form-text text-muted">Use at least 15 alphanumeric characters and symbols</div>
                    <div class="small text-danger" id="newPasswordError"></div>
                    
                </div>
                
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label fw-bold">
                        Confirm Password <span class="text-danger">*</span>
                        <span class="material-symbols-outlined text-primary align-middle" style="font-size: 1rem; cursor: pointer;">
                            info
                        </span>
                    </label>
                    <div class="input-group">
                        <input type="password" id="confirmPassword" placeholder="Confirm Password" name="confirmPassword"
                            class="form-control p-2 border-end-0" required>
                        <span class="input-group-text bg-white border-start-0" style="cursor: pointer;" id="toggleConfirmPassword">
                            <span class="material-symbols-outlined text-secondary">
                                visibility
                            </span>
                        </span>
                    </div>
                    <div class="small text-danger" id="confirmPasswordError"></div>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-success w-100" onclick="submitNewPassword()">Submit</button>
                </div>
            </div>
        </div>
        <?php include 'components/messageModal.php' ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script>
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

        async function submitNewPassword() {
            const newPasswordInput = document.getElementById('newPassword');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const newPassword = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const newPasswordError = document.getElementById('newPasswordError');
            const confirmPasswordError = document.getElementById('confirmPasswordError');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const toggleNewPassword = document.getElementById('toggleNewPassword');

            newPasswordInput.style.border = '';
            confirmPasswordInput.style.border = '';
            newPasswordError.textContent = '';
            confirmPasswordError.textContent = '';
            toggleNewPassword.classList.remove('border-danger', 'border-1');
            toggleConfirmPassword.classList.remove('border-danger', 'border-1');

            if (newPassword !== confirmPassword) {
                newPasswordInput.style.border = '1px solid red';
                newPasswordError.textContent = 'Passwords do not match.';
                toggleNewPassword.classList.add('border-danger', 'border-1');
                toggleConfirmPassword.classList.add('border-danger', 'border-1');
                confirmPasswordInput.style.border = '1px solid red';
                confirmPasswordError.textContent = 'Passwords do not match.';
                return;
            }

            if (newPassword.length < 15) {
                newPasswordInput.style.border = '1px solid red';
                newPasswordError.textContent = 'Password must be at least 15 characters long.';
                toggleNewPassword.classList.add('border-danger', 'border-1');
                return;
            }

            try {
                const response = await fetch('../api/resetPassword.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        new_password: newPassword
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showMessage('success', 'Password Reset', 'Your password has been reset successfully. Please log in with your new password.', 'OK');
                    window.location.href = 'login.php';
                } else {
                    alert(result.error || 'Failed to reset password. Please try again.');
                }

            } catch (error) {
                console.error('Error:', error);
            }
        }

    </script>
</body>

</html>