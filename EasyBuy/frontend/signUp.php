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
            background-color: #4caf50;
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
                <img src="../assets/easybuylogo.svg" class="img-fluid" alt="Sign Up Image"
                    style="height:600px; width: 600px; object-fit: cover;">
            </div>
            <div class="col-12 col-lg-7 col-md-12 p-lg-5">
                <h1 class="text-center fw-bold mt-5 mb-4">Sign Up</h1>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger mx-3" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="signupForm">
                    <div class="card mx-3 p-5 rounded-4">

                        <div id="step1" class="step-content">
                            <div class="d-flex align-items-baseline mb-4">
                                <h3 class="text-start mb-0 me-2">Profile</h3>
                                <small class="text-muted fw-normal">*Required Fields have asterisks</small>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                    <label for="lastName" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName"
                                        placeholder="Enter Last Name" required>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="firstName" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName"
                                        placeholder="Enter First Name" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                    <label for="contact" class="form-label">Contact Number *</label>
                                    <input type="tel" class="form-control" id="contact" name="contact"
                                        placeholder="Enter Contact Number" required>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Enter Email" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="password" class="form-label">Password *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Enter Password" required>
                                        <button type="button" id="togglePassword" class="btn btn-outline-secondary">
                                            <i id="toggleIcon1" class="bi bi-eye-fill"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-1 ms-2 fw-normal">Use at least 15 alphanumeric
                                        characters and symbols</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label for="confirm_password" class="form-label">Confirm Password *
                                        <button type="button" class="btn btn-link btn-sm p-0 ms-1"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="For verification, enter the same password again.">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_password"
                                            placeholder="Confirm Password" required>
                                        <button type="button" id="toggleConfirmPassword"
                                            class="btn btn-outline-secondary">
                                            <i id="toggleIcon2" class="bi bi-eye-fill"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="step2" class="step-content" style="display: none;">
                            <h3 class="text-start mb-4">Address</h3>
                            <div class="row mb-3">
                                <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                    <label for="houseNumber" class="form-label">House Number *</label>
                                    <input type="text" class="form-control" id="houseNumber" name="houseNumber"
                                        placeholder="#7" required>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="street" class="form-label">Street *</label>
                                    <input type="text" class="form-control" id="street" name="street"
                                        placeholder="Zone - 1" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                    <label for="lot" class="form-label">Lot</label>
                                    <input type="text" class="form-control" id="lot" name="lot" placeholder="">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="block" class="form-label">Block</label>
                                    <input type="text" class="form-control" id="block" name="block" placeholder="">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                    <label for="barangay" class="form-label">Barangay *</label>
                                    <input type="text" class="form-control" id="barangay" name="barangay"
                                        placeholder="Altura Bata" required>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="city" class="form-label">City *</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        placeholder="Tanauan City" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                    <label for="province" class="form-label">Province *</label>
                                    <input type="text" class="form-control" id="province" name="province"
                                        placeholder="Batangas" required>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="postal" class="form-label">Postal</label>
                                    <input type="text" class="form-control" id="postal" name="postal"
                                        placeholder="4232">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center my-4">
                        <span class="dot active" data-step="1"></span>
                        <span class="dot" data-step="2"></span>
                    </div>

                    <div class="d-flex justify-content-between mx-3">
                        <button type="button" id="backBtn" class="btn btn-success px-4 py-2"
                            style="display: none;">BACK</button>
                        <button type="button" id="nextBtn" class="btn btn-success px-4 py-2 ms-auto">NEXT</button>
                        <button type="submit" id="submitBtn" class="btn btn-success px-4 py-2 ms-auto"
                            style="display: none;">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="errorModal" class="modal fade" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 rounded-5 shadow text-center border-0">
                <div class="mb-3">
                    <i class="bi bi-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                </div>
                <div class="h5 fw-bold mb-2">Unable to create your account</div>
                <p class="mb-4">We couldn't complete your account setup due to a technical issue on our end. The
                    information will be retained, and nothing was lost. Please try creating your account again,
                    or return to the previous step if you want to make changes. If the issue continues, contact
                    Customer Support located in the menu bar.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4" id="tryAgainBtn">Try Again</button>
                </div>
            </div>
        </div>
    </div>

    <div id="userExistModal" class="modal fade" tabindex="-1" aria-labelledby="userExistModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 rounded-5 shadow text-center border-0">
                <div class="mb-3">
                    <i class="bi bi-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                </div>
                <div class="h5 fw-bold mb-2">Email Already Exists</div>
                <p class="mb-4">The email address you entered is already registered in our system.
                    Please use a different email address or try logging in if you already have an account.
                    If you believe this is an error, contact Customer Support located in the menu bar.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                    <a href="login.php" class="btn btn-success px-4">Go to Login</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        var currentStep = 1;
        const totalSteps = 2;

        function showStep(step) {
            document.querySelectorAll('.step-content').forEach(el => el.style.display = 'none');

            document.getElementById(`step${step}`).style.display = 'block';

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
                backBtn.style.display = 'none';
                nextBtn.style.display = 'block';
                submitBtn.style.display = 'none';
            } else if (step === totalSteps) {
                backBtn.style.display = 'block';
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'block';
            } else {
                backBtn.style.display = 'block';
                nextBtn.style.display = 'block';
                submitBtn.style.display = 'none';
            }
        }

        document.getElementById('nextBtn').addEventListener('click', async function () {
            if (currentStep === 1) {
                const email = document.getElementById('email').value;

                if (!email) {
                    alert('Please enter an email');
                    return;
                }

                try {
                    const response = await fetch(`signUp.php?check_email=${encodeURIComponent(email)}`);
                    const data = await response.json();
                    
                    console.log('Email check response:', data);

                    if (data.exists) {
                        const modalElement = document.getElementById('userExistModal');
                        let userExistsModal = bootstrap.Modal.getInstance(modalElement);
                        
                        if (!userExistsModal) {
                            userExistsModal = new bootstrap.Modal(modalElement);
                        }
                        
                        userExistsModal.show();
                        return;
                    }
                } catch (error) {
                    console.error('Error checking email:', error);
                    alert('Error checking email. Please try again.');
                    return;
                }
            }

            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        });

        document.getElementById('backBtn').addEventListener('click', function () {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });

        document.getElementById('submitBtn').addEventListener('click', function (e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return false;
            }

            document.getElementById('signupForm').submit();
        });

        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon1');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye-fill', !isHidden);
            icon.classList.toggle('bi-eye-slash-fill', isHidden);
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
            const input = document.getElementById('confirm_password');
            const icon = document.getElementById('toggleIcon2');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye-fill', !isHidden);
            icon.classList.toggle('bi-eye-slash-fill', isHidden);
        });
        <?php if (isset($error)): ?>
            document.addEventListener('DOMContentLoaded', function() {
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            });
        <?php endif; ?>

        document.getElementById('tryAgainBtn')?.addEventListener('click', function () {
            var errorModal = bootstrap.Modal.getInstance(document.getElementById('errorModal'));
            if (errorModal) {
                errorModal.hide();
            }
            location.reload();
        });
    </script>
</body>

</html>