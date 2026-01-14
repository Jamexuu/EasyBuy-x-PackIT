<?php
require_once '../api/classes/Auth.php';
require_once '../api/classes/User.php';

if (isset($_GET['check_email'])) {
    header('Content-Type: application/json');
    $user = new User();
    $exists = $user->emailExists($_GET['check_email']);
    echo json_encode(['exists' => $exists]);
    exit();
}

Auth::redirectIfLoggedIn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userData = [
        'firstName' => $_POST['firstName'],
        'lastName' => $_POST['lastName'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'contactNumber' => $_POST['contact']
    ];

    $addressData = [
        'houseNumber' => $_POST['houseNumber'],
        'street' => $_POST['street'],
        'lot' => $_POST['lot'] ?? '',
        'block' => $_POST['block'] ?? '',
        'barangay' => $_POST['barangay'],
        'city' => $_POST['city'],
        'province' => $_POST['province'],
        'postalCode' => $_POST['postal'] ?? ''
    ];

    try {
        $user = new User();
        $user->register($userData, $addressData);

        $loginResult = $user->login($userData['email'], $userData['password']);

        if ($loginResult) {
            Auth::login($loginResult['id'], $loginResult['email'], $loginResult['first_name']);
            header("Location: ../index.php");
            exit();
        }
    } catch (Exception $e) {
        $error = "Registration failed. Please try again.";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up - Easy Buy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dot {
            height: 12px;
            width: 12px;
            margin: 0 4px;
            background-color: #ddd;
            border-radius: 50%;
            display: inline-block;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .dot.active {
            background-color: #198754;
        }
        .step-content {
            min-height: 400px;
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col">
                <div class="p-5 gradient-banner" style="background: var(--gradient-color);"></div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-lg-5 col-md-12 p-5">
                <img src="../assets/easybuylogo.svg" class="img-fluid" alt="Easy Buy Logo"
                    style="height:600px; width: 600px; object-fit: cover;">
            </div>

            <div class="col-12 col-lg-7 col-md-12 p-3 p-lg-5">
                <h1 class="text-center fw-bold mt-5 mb-4">Sign Up</h1>

                <form method="POST" action="" id="signupForm">
                    <div class="card mx-3 p-4 p-lg-5 rounded-4 shadow-sm">
                        <div id="step1" class="step-content">
                            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-baseline mb-4 gap-2">
                                <h3 class="text-start mb-0 me-2">Profile</h3>
                                <small class="text-muted fw-normal">*Required Fields</small>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                    <label for="lastName" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName"
                                        placeholder="Enter Last Name" required>
                                    <div class="invalid-feedback d-none"></div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="firstName" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName"
                                        placeholder="Enter First Name" required>
                                    <div class="invalid-feedback d-none"></div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                    <label for="contact" class="form-label">Contact Number *</label>
                                    <input type="tel" class="form-control" id="contact" name="contact"
                                        placeholder="09123456789" required maxlength="11">
                                    <div class="invalid-feedback d-none"></div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="example@email.com" required>
                                    <div class="invalid-feedback d-none"></div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="password" class="form-label">Password *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Enter Password" required>
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-1">Use at least 8 characters with letters, numbers and symbols</small>
                                    <div class="invalid-feedback d-none"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <label for="confirm_password" class="form-label">
                                        Confirm Password *
                                        <button type="button" class="btn btn-link btn-sm p-0 ms-1"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Enter the same password again for verification">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_password"
                                            placeholder="Confirm Password" required>
                                        <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword">
                                            <i class="bi bi-eye-slash-fill"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback d-none"></div>
                                </div>
                            </div>
                        </div>

                        <div id="step2" class="step-content d-none">
                            <h3 class="text-start mb-4">Address</h3>

                            <div class="row mb-3">
                                <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                    <label for="houseNumber" class="form-label">House Number *</label>
                                    <input type="text" class="form-control" id="houseNumber" name="houseNumber"
                                        placeholder="#7" required>
                                    <div class="invalid-feedback d-none"></div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="street" class="form-label">Street *</label>
                                    <input type="text" class="form-control" id="street" name="street"
                                        placeholder="Zone - 1" required>
                                    <div class="invalid-feedback d-none"></div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                    <label for="lot" class="form-label">Lot</label>
                                    <input type="text" class="form-control" id="lot" name="lot" placeholder="Optional">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="block" class="form-label">Block</label>
                                    <input type="text" class="form-control" id="block" name="block" placeholder="Optional">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                    <label for="barangay" class="form-label">Barangay *</label>
                                    <input type="text" class="form-control" id="barangay" name="barangay"
                                        placeholder="Altura Bata" required>
                                    <div class="invalid-feedback d-none"></div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="city" class="form-label">City *</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        placeholder="Tanauan City" required>
                                    <div class="invalid-feedback d-none"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                    <label for="province" class="form-label">Province *</label>
                                    <input type="text" class="form-control" id="province" name="province"
                                        placeholder="Batangas" required>
                                    <div class="invalid-feedback d-none"></div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="postal" class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" id="postal" name="postal"
                                        placeholder="4232" maxlength="4">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center my-4">
                        <span class="dot active" data-step="1"></span>
                        <span class="dot" data-step="2"></span>
                    </div>

                    <div class="d-flex justify-content-between mx-3 gap-2">
                        <button type="button" id="backBtn" class="btn btn-success px-4 py-2 d-none">BACK</button>
                        <button type="button" id="nextBtn" class="btn btn-success px-4 py-2 ms-auto">NEXT</button>
                        <button type="submit" id="submitBtn" class="btn btn-success px-4 py-2 ms-auto d-none">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="userExistModal" class="modal fade" tabindex="-1" aria-labelledby="userExistModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 rounded-4 shadow border-0">
                <div class="text-center mb-3">
                    <i class="bi bi-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-center fw-bold mb-2">Email Already Exists</h5>
                <p class="text-center mb-4">The email address you entered is already registered. Please use a different email or try logging in.</p>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                    <a href="login.php" class="btn btn-success px-4">Go to Login</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/messageModal.php' ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));

        let currentStep = 1;
        const totalSteps = 2;

        const validationRules = {
            firstName: {
                required: true,
                message: 'First name is required'
            },
            lastName: {
                required: true,
                message: 'Last name is required'
            },
            contact: {
                required: true,
                pattern: /^09\d{9}$/,
                message: 'Please enter a valid Philippine mobile number (e.g., 09123456789)'
            },
            email: {
                required: true,
                pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                message: 'Please enter a valid email address'
            },
            password: {
                required: true,
                minLength: 8,
                pattern: /^(?=.*[a-zA-Z])(?=.*\d)/,
                message: 'Password must be at least 8 characters with letters, numbers, and symbols'
            },
            confirm_password: {
                required: true,
                match: 'password',
                message: 'Passwords do not match'
            },
            houseNumber: {
                required: true,
                message: 'House number is required'
            },
            street: {
                required: true,
                message: 'Street is required'
            },
            barangay: {
                required: true,
                message: 'Barangay is required'
            },
            city: {
                required: true,
                message: 'City is required'
            },
            province: {
                required: true,
                message: 'Province is required'
            }
        };

        function showError(input, message) {
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = message;
                feedback.classList.remove('d-none');
                feedback.classList.add('d-block');
            }
            input.classList.add('is-invalid');
        }

        function clearError(input) {
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.classList.remove('d-block');
                feedback.classList.add('d-none');
            }
            input.classList.remove('is-invalid');
        }

        function validateField(fieldName) {
            const input = document.getElementById(fieldName);
            const rules = validationRules[fieldName];
            
            if (!input || !rules) return true;
            
            const value = input.value.trim();
            
            clearError(input);
            
            if (rules.required && !value) {
                showError(input, rules.message);
                return false;
            }
            
            if (!value) return true;
            
            if (rules.minLength && value.length < rules.minLength) {
                showError(input, rules.message);
                return false;
            }
            
            if (rules.pattern && !rules.pattern.test(value)) {
                showError(input, rules.message);
                return false;
            }
            
            if (rules.match) {
                const matchInput = document.getElementById(rules.match);
                if (value !== matchInput.value) {
                    showError(input, rules.message);
                    return false;
                }
            }
            
            return true;
        }

        function validateStep(step) {
            let isValid = true;
            let fields = [];
            
            if (step === 1) {
                fields = ['firstName', 'lastName', 'contact', 'email', 'password', 'confirm_password'];
            } else if (step === 2) {
                fields = ['houseNumber', 'street', 'barangay', 'city', 'province'];
            }
            
            fields.forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });
            
            return isValid;
        }

        // Show step
        function showStep(step) {
            document.querySelectorAll('.step-content').forEach(el => {
                el.classList.add('d-none');
            });
            
            document.getElementById(`step${step}`).classList.remove('d-none');
            
            document.querySelectorAll('.dot').forEach(dot => {
                dot.classList.remove('active');
                if (parseInt(dot.dataset.step) === step) {
                    dot.classList.add('active');
                }
            });
            
            const backBtn = document.getElementById('backBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            
            if (step === 1) {
                backBtn.classList.add('d-none');
                nextBtn.classList.remove('d-none');
                submitBtn.classList.add('d-none');
            } else if (step === totalSteps) {
                backBtn.classList.remove('d-none');
                nextBtn.classList.add('d-none');
                submitBtn.classList.remove('d-none');
            }
        }

        function initializeValidation() {
            Object.keys(validationRules).forEach(fieldName => {
                const input = document.getElementById(fieldName);
                if (input) {
                    input.addEventListener('blur', () => validateField(fieldName));
                    input.addEventListener('input', () => {
                        if (input.classList.contains('is-invalid')) {
                            clearError(input);
                        }
                    });
                    
                    // Password match validation
                    if (fieldName === 'password') {
                        input.addEventListener('input', () => {
                            const confirmPassword = document.getElementById('confirm_password');
                            if (confirmPassword.value) {
                                validateField('confirm_password');
                            }
                        });
                    }
                }
            });
        }

        document.getElementById('nextBtn').addEventListener('click', async function() {
            if (!validateStep(currentStep)) {
                return;
            }
            
            // Check email exists
            if (currentStep === 1) {
                const email = document.getElementById('email').value;
                
                try {
                    const response = await fetch(`signUp.php?check_email=${encodeURIComponent(email)}`);
                    const data = await response.json();
                    
                    if (data.exists) {
                        const modal = new bootstrap.Modal(document.getElementById('userExistModal'));
                        modal.show();
                        return;
                    }
                } catch (error) {
                    console.error('Error checking email:', error);
                    if (typeof showMessage === 'function') {
                        showMessage('Error', 'Unable to verify email. Please try again.', 'error');
                    }
                    return;
                }
            }
            
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        });

        document.getElementById('backBtn').addEventListener('click', function() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });

        document.getElementById('submitBtn').addEventListener('click', function(e) {
            e.preventDefault();
            
            if (!validateStep(2)) {
                return;
            }
            
            if (!validateField('password') || !validateField('confirm_password')) {
                currentStep = 1;
                showStep(currentStep);
                return;
            }
            
            document.getElementById('signupForm').submit();
        });

        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            const isPassword = input.type === 'password';
            
            input.type = isPassword ? 'text' : 'password';
            icon.className = isPassword ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill';
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const input = document.getElementById('confirm_password');
            const icon = this.querySelector('i');
            const isPassword = input.type === 'password';
            
            input.type = isPassword ? 'text' : 'password';
            icon.className = isPassword ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill';
        });

        // Show error modal if registration failed
        <?php if (isset($error)): ?>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof showMessage === 'function') {
                    showMessage('Registration Failed', '<?php echo addslashes($error); ?>', 'error');
                }
            });
        <?php endif; ?>

        document.addEventListener('DOMContentLoaded', function() {
            initializeValidation();
        });
    </script>
</body>

</html>