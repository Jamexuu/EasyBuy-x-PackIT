<?php
session_start();

require_once __DIR__ . '/../api/classes/Database.php';
require_once __DIR__ . '/../api/gmail/sendMail.php';

$db = new Database();
$error = "";

// Already logged in
if (isset($_SESSION['driver_id'])) {
    header("Location: driver.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['firstName'] ?? '');
    $last_name  = trim($_POST['lastName'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $contact    = trim($_POST['contact'] ?? '');

    $house_num  = trim($_POST['house_num'] ?? '');
    $street     = trim($_POST['street'] ?? '');
    $province   = trim($_POST['province'] ?? '');
    $city       = trim($_POST['city'] ?? '');
    $barangay   = trim($_POST['barangay'] ?? '');

    $vehicle_type  = trim($_POST['vehicle_type'] ?? '');
    $license_plate = trim($_POST['license_plate'] ?? '');
    $password      = (string)($_POST['password'] ?? '');

    // Strict validate vehicle type matching your UI options
    $allowedVehicleTypes = ['Motorcycle', 'Tricycle', 'Sedan', 'Pick-up Truck', 'Closed Van', 'Forward Truck'];

    if (!in_array($vehicle_type, $allowedVehicleTypes, true)) {
        $error = "Please select a valid vehicle type.";
    }

    if ($error === "") {
        if (
            $first_name === '' || $last_name === '' || $email === '' || $contact === '' ||
            $house_num === '' || $street === '' || $province === '' || $city === '' || $barangay === '' ||
            $vehicle_type === '' || $license_plate === '' || $password === ''
        ) {
            $error = "Please fill in all required fields.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email address.";
        } elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters.";
        } else {
            // Check duplicate email
            $stmt = $db->executeQuery("SELECT id FROM drivers WHERE email = ? LIMIT 1", [$email]);
            $rows = $db->fetch($stmt);

            if (!empty($rows)) {
                $error = "Email is already registered as a driver.";
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);

                // Insert into drivers
                $db->executeQuery(
                    "INSERT INTO drivers 
                    (first_name, last_name, email, password, contact_number, house_number, street, province, city, barangay, vehicle_type, license_plate, is_available, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())",
                    [
                        $first_name,
                        $last_name,
                        $email,
                        $hash,
                        $contact,
                        $house_num,
                        $street,
                        $province,
                        $city,
                        $barangay,
                        $vehicle_type,
                        $license_plate
                    ]
                );

                $driver_id = $db->lastInsertId();

                // Send welcome email
                try {
                    $subject = "Welcome to PackIT - Driver Partner Account Created";
                    $html = '
                        <div style="font-family: Arial, sans-serif; line-height: 1.6; color: #111;">
                            <h2 style="margin:0 0 10px;">Welcome to PackIT, ' . htmlspecialchars($firstName) . '!</h2>
                            <p>We’re excited to have you as a <strong>Driver Partner</strong>.</p>
                            <div style="background:#fce354; padding:14px; border-radius:12px; margin:14px 0;">
                                <p style="margin:0;"><strong>Vehicle Type:</strong> ' . htmlspecialchars($vehicle_type) . '</p>
                                <p style="margin:0;"><strong>License Plate:</strong> ' . htmlspecialchars($license_plate) . '</p>
                            </div>
                            <p>You can now log in to your Driver Portal and start accepting deliveries.</p>
                        </div>';
                    sendMail($email, $subject, $html);
                } catch (Exception $e) {
                }

                // Auto-login
                $_SESSION['driver_id'] = (int)$driver_id;
                header("Location: driver.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Join PackIT - Driver Partner Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <style>
        /* Copied/adapted UI tokens and styles from the Create Account UI */
        :root {
            --packit-yellow: #f5d84b;
            --packit-blue: #f5d84b;
            /* keep parity with original token — adjust if you prefer a blue */
        }

        body {
            background-color: #f8f9fa;
        }

        .card {
            border: none;
        }

        /* Header / section styles */
        .section-header {
            display: flex;
            align-items: center;
            gap: .75rem;
            color: var(--packit-yellow);
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: .5rem;
        }

        .section-header .bi {
            background: rgba(245, 216, 75, 0.12);
            border-radius: 50%;
            padding: .25rem;
            font-size: 1.2rem;
            color: var(--packit-yellow);
        }

        .section-submeta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: .75rem;
            margin-bottom: 1rem;
        }

        .section-submeta .required {
            color: #6c757d;
            font-size: .95rem;
        }

        /* Inputs: rounded, subtle border, larger padding */
        .form-control {
            border-radius: .5rem;
            border: 1px solid #e6e9ee;
            padding: .75rem 1rem;
            background-color: #fff;
        }

        .form-select {
            border-radius: .5rem;
            border: 1px solid #e6e9ee;
            padding: .65rem 1rem;
            background-color: #fff;
        }

        .input-group .form-control {
            border-top-left-radius: .5rem;
            border-bottom-left-radius: .5rem;
            border-left: none;
        }

        .input-group .input-group-text {
            border-radius: .5rem 0 0 .5rem;
            border: 1px solid #e6e9ee;
            background: #fff;
        }

        label.form-label {
            font-weight: 600;
            margin-bottom: .4rem;
            display: block;
        }

        .step-content {
            min-height: 420px;
            animation: fadeIn 0.35s;
        }

        .dot {
            height: 12px;
            width: 12px;
            margin: 0 4px;
            background-color: #ddd;
            border-radius: 50%;
            display: inline-block;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .dot.active {
            background-color: var(--packit-yellow);
            transform: scale(1.2);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--packit-yellow);
            box-shadow: 0 0 0 0.25rem rgba(245, 216, 75, 0.12);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Validation overrides (keeps bootstrap invalid look but with stronger border) */
        .is-invalid {
            border-color: #dc3545 !important;
            background-image: none;
        }

        .is-invalid:focus {
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.12) !important;
        }

        .form-text.small {
            margin-top: .25rem;
            color: #6c757d;
        }

        /* Card sizing: use max-width and full width for responsiveness */
        .card-main {
            max-width: 700px;
            width: 100%;
        }

        /* Small screens adjustments */
        @media (max-width: 576px) {
            .section-header {
                font-size: 1.1rem;
            }

            .form-control {
                padding: .55rem .75rem;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col">
                <div class="gradient-banner"></div>
            </div>
        </div>
    </div>

    <div class="container-fluid h-100">
        <div class="row min-vh-100 align-items-center">
            <div class="col-12 py-5">
                <div class="d-flex justify-content-center">
                    <div class="card border-0 rounded-4 card-main">
                        <div class="card-body p-4 p-md-5">
                            <h1 class="text-center fw-bold mb-2">Driver Account</h1>
                            <p class="text-center text-muted mb-5">Join PackIT and start earning</p>

                            <?php if ($error): ?>
                                <div class="alert alert-danger mb-4"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>

                            <form action="signup.php" method="POST" id="DriverProfile" novalidate>

                                <div id="profileHeader" class="section-submeta">
                                    <div class="section-header">
                                        <i class="bi bi-person-circle"></i>
                                        <span>Profile &amp; Security</span>
                                    </div>
                                    <div class="required">*Required</div>
                                </div>

                                <!-- STEP 1 -->
                                <div id="step1" class="step-content">
                                    <div class="row mb-3">
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <label for="firstName" class="form-label">First Name *</label>
                                            <input type="text" class="form-control" id="firstName" name="firstName"
                                                placeholder="e.g. Juan" required>
                                            <div class="invalid-feedback">Please enter your first name.</div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="lastName" class="form-label">Last Name *</label>
                                            <input type="text" class="form-control" id="lastName" name="lastName"
                                                placeholder="e.g. Dela Cruz" required>
                                            <div class="invalid-feedback">Please enter your last name.</div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <!-- Mobile: label above, input-group with +63 prefix and placeholder example -->
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <label for="contactVisible" class="form-label">Mobile Number *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">+63</span>
                                                <input type="tel" inputmode="numeric" pattern="\d{10}"
                                                    class="form-control" id="contactVisible"
                                                    placeholder="e.g. 9913389514" maxlength="10" required
                                                    aria-describedby="contactHelp">
                                            </div>
                                            <input type="hidden" id="contact" name="contact" value="">
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <label for="email" class="form-label">Email Address *</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="e.g. name@example.com" required>
                                            <div class="invalid-feedback">Please enter a valid email address.</div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label for="password" class="form-label">Password *</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="password" name="password"
                                                    placeholder="Create a strong password" required>
                                                <button type="button" id="togglePassword" class="btn btn-outline-secondary">
                                                    <i id="toggleIcon1" class="bi bi-eye-fill"></i>
                                                </button>
                                            </div>
                                            <div class="form-text small mt-1">Must be at least 8 characters with letters and numbers.</div>
                                            <div class="invalid-feedback" id="passwordFeedback">Password must be at least 8 characters and include letters and numbers.</div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <label for="confirm_password" class="form-label">Confirm Password *</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="confirmPassword"
                                                    name="confirm_password" placeholder="Re-enter password" required>
                                                <button type="button" id="toggleConfirmPassword" class="btn btn-outline-secondary">
                                                    <i id="toggleIcon2" class="bi bi-eye-fill"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback" id="confirmFeedback">Passwords do not match.</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- STEP 2 -->
                                <div id="step2" class="step-content" style="display:none;">
                                    <div class="section-header mb-3">
                                        <i class="bi bi-truck"></i>
                                        <span>Vehicle &amp; Address</span>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Vehicle Type *</label>
                                            <select name="vehicle_type" id="vehicleType" class="form-select" required>
                                                <option value="" disabled selected>Select Vehicle</option>
                                                <option value="Motorcycle">Motorcycle</option>
                                                <option value="Tricycle">Tricycle</option>
                                                <option value="Sedan">Sedan</option>
                                                <option value="Pick-up Truck">Pick-up Truck</option>
                                                <option value="Closed Van">Closed Van</option>
                                                <option value="Forward Truck">Forward Truck</option>
                                            </select>
                                            <div class="invalid-feedback">Please select your vehicle type.</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">License Plate *</label>
                                            <input type="text" class="form-control" id="licensePlate" name="license_plate" placeholder="ABC 1234" required>
                                            <div class="invalid-feedback">Please enter your license plate.</div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">House/Unit No. *</label>
                                            <input type="text" class="form-control" id="houseNum" name="house_num" placeholder="#123" required>
                                            <div class="invalid-feedback">Please enter house/unit number.</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Street *</label>
                                            <input type="text" class="form-control" id="street" name="street" placeholder="Maple St." required>
                                            <div class="invalid-feedback">Please enter a street name.</div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <label for="provinceSelect" class="form-label">Province *</label>
                                            <select class="form-select" id="provinceSelect" required>
                                                <option value="" selected disabled>Loading...</option>
                                            </select>
                                            <div class="invalid-feedback">Please choose a province.</div>
                                            <input type="hidden" id="province" name="province" value="">
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <label for="citySelect" class="form-label">City/Municipality *</label>
                                            <select class="form-select" id="citySelect" disabled required>
                                                <option value="" selected disabled>Select Province first</option>
                                            </select>
                                            <div class="invalid-feedback">Please choose a city/municipality.</div>
                                            <input type="hidden" id="city" name="city" value="">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <label for="barangaySelect" class="form-label">Barangay *</label>
                                            <select class="form-select" id="barangaySelect" disabled required>
                                                <option value="" selected disabled>Select City first</option>
                                            </select>
                                            <div class="invalid-feedback">Please choose a barangay.</div>
                                            <input type="hidden" id="barangay" name="barangay" value="">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="postal" class="form-label">Zip Code</label>
                                            <input type="text" class="form-control" id="postal" name="postal" placeholder="4234">
                                        </div>
                                    </div>

                                    <div class="mt-4 p-3 bg-light rounded text-center">
                                        <p class="mb-0 small text-muted">
                                            By clicking submit, you agree to PackIT's <a href="#">Driver Partner Terms</a>.
                                        </p>
                                    </div>
                                </div>

                                <div class="text-center my-4">
                                    <span class="dot active" data-step="1"></span>
                                    <span class="dot" data-step="2"></span>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" id="backBtn" class="btn btn-outline-secondary px-4 py-2" style="display:none;">
                                        <i class="bi bi-arrow-left"></i> Back
                                    </button>

                                    <button type="button" id="nextBtn" class="btn btn-warning px-4 py-2 ms-auto">
                                        Next <i class="bi bi-arrow-right"></i>
                                    </button>

                                    <button type="submit" id="submitBtn" class="btn btn-warning px-5 py-2 fw-bold ms-auto" style="display:none;">
                                        CREATE DRIVER ACCOUNT
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Stepper state
        let currentStep = 1;
        const totalSteps = 2;

        const form = document.getElementById('DriverProfile');

        function capitalizeWords(input) {
            input.value = input.value
                .toLowerCase()
                .replace(/\b\w/g, char => char.toUpperCase());
        }

        const firstNameInput = document.getElementById('firstName');
        const lastNameInput = document.getElementById('lastName');

        if (firstNameInput) {
            firstNameInput.addEventListener('input', function() {
                capitalizeWords(this);
            });
        }

        if (lastNameInput) {
            lastNameInput.addEventListener('input', function() {
                capitalizeWords(this);
            });
        }

        /* ===============================
       LICENSE PLATE FORMATTER
       - Max 7 chars (spaces included)
       - Uppercase
       - Letters, numbers, space only
    =============================== */
        const licensePlateInput = document.getElementById('licensePlate');

        if (licensePlateInput) {
            licensePlateInput.addEventListener('input', function() {
                let value = this.value.toUpperCase();

                // allow letters, numbers, and spaces only
                value = value.replace(/[^A-Z0-9 ]/g, '');

                // limit to 7 characters (including spaces)
                this.value = value.slice(0, 8);

                this.classList.remove('is-invalid');
            });

            licensePlateInput.addEventListener('paste', function(e) {
                e.preventDefault();
                let text = (e.clipboardData || window.clipboardData).getData('text');
                text = text.toUpperCase().replace(/[^A-Z0-9 ]/g, '').slice(0, 8);
                this.value = text;
            });
        }

        function showStep(step) {
            document.querySelectorAll('.step-content').forEach(el => el.style.display = 'none');
            const currentSection = document.getElementById(`step${step}`);
            if (currentSection) currentSection.style.display = 'block';

            document.querySelectorAll('.dot').forEach(dot => {
                dot.classList.remove('active');
                if (parseInt(dot.dataset.step) === step) dot.classList.add('active');
            });

            document.getElementById('backBtn').style.display = step === 1 ? 'none' : 'block';
            document.getElementById('nextBtn').style.display = step === 1 ? 'block' : 'none';
            document.getElementById('submitBtn').style.display = step === totalSteps ? 'block' : 'none';

            // hide profile header on step 2 (like original)
            const profileHeader = document.getElementById('profileHeader');
            if (profileHeader) profileHeader.style.display = (step === 1) ? 'flex' : 'none';

            // scroll mobile-friendly
            if (currentSection) currentSection.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }

        // Validation helpers
        function clearInvalid(container) {
            container.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        }

        function validateStep1() {
            let valid = true;
            const firstName = document.getElementById('firstName');
            const lastName = document.getElementById('lastName');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirm = document.getElementById('confirmPassword');
            const contactVisible = document.getElementById('contactVisible');
            const contactFeedback = document.getElementById('contactFeedback');

            [firstName, lastName, email, password, confirm, contactVisible].forEach(el => {
                if (el) el.classList.remove('is-invalid');
            });

            if (!firstName.value.trim()) {
                firstName.classList.add('is-invalid');
                valid = false;
            }
            if (!lastName.value.trim()) {
                lastName.classList.add('is-invalid');
                valid = false;
            }
            if (!email.checkValidity()) {
                email.classList.add('is-invalid');
                valid = false;
            }

            // contact: must be exactly 10 digits visible (after +63)
            const contactVal = contactVisible.value.replace(/\D/g, '');
            if (contactVal.length !== 10) {
                contactVisible.classList.add('is-invalid');
                if (contactFeedback) contactFeedback.textContent = "Please enter exactly 10 digits after +63 (e.g. 9913389514).";
                valid = false;
            }

            // password rules: at least 8 chars, letters and numbers
            const passVal = password.value;
            const passRegex = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;
            if (!passRegex.test(passVal)) {
                password.classList.add('is-invalid');
                document.getElementById('passwordFeedback').textContent = "Password must be at least 8 characters and include both letters and numbers.";
                valid = false;
            }

            // confirm password match
            if (confirm.value !== passVal || !confirm.value) {
                confirm.classList.add('is-invalid');
                document.getElementById('confirmFeedback').textContent = "Passwords do not match.";
                valid = false;
            }

            if (!valid) {
                const firstInvalid = document.querySelector('.is-invalid');
                if (firstInvalid) firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }

            return valid;
        }

        function validateStep2() {
            let valid = true;
            const house = document.getElementById('houseNum');
            const street = document.getElementById('street');
            const provinceSelect = document.getElementById('provinceSelect');
            const citySelect = document.getElementById('citySelect');
            const barangaySelect = document.getElementById('barangaySelect');
            const vehicleType = document.getElementById('vehicleType');
            const licensePlate = document.getElementById('licensePlate');

            [house, street, provinceSelect, citySelect, barangaySelect, vehicleType, licensePlate].forEach(el => {
                if (el) el.classList.remove('is-invalid');
            });

            if (!house.value.trim()) {
                house.classList.add('is-invalid');
                valid = false;
            }
            if (!street.value.trim()) {
                street.classList.add('is-invalid');
                valid = false;
            }
            if (!provinceSelect.value) {
                provinceSelect.classList.add('is-invalid');
                valid = false;
            }
            if (!citySelect.value) {
                citySelect.classList.add('is-invalid');
                valid = false;
            }
            if (!barangaySelect.value) {
                barangaySelect.classList.add('is-invalid');
                valid = false;
            }
            if (!vehicleType.value) {
                vehicleType.classList.add('is-invalid');
                valid = false;
            }
            if (!licensePlate.value.trim()) {
                licensePlate.classList.add('is-invalid');
                valid = false;
            }

            if (!valid) {
                const firstInvalid = document.querySelector('.is-invalid');
                if (firstInvalid) firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
            return valid;
        }

        // Next / Back handlers
        document.getElementById('nextBtn').addEventListener('click', function() {
            if (currentStep < totalSteps) {
                if (currentStep === 1) {
                    if (!validateStep1()) return;
                    // Prepare hidden contact field in server format: 0 + visible 10 digits
                    const visible = document.getElementById('contactVisible').value.replace(/\D/g, '').slice(0, 10);
                    document.getElementById('contact').value = visible ? '0' + visible : '';
                }
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

        // Submit handler: validate step2 and ensure contact hidden is set
        form.addEventListener('submit', function(e) {
            // If user is still on step 1, run step1 validation then advance
            if (currentStep === 1) {
                e.preventDefault();
                if (!validateStep1()) return;
                const visible = document.getElementById('contactVisible').value.replace(/\D/g, '').slice(0, 10);
                document.getElementById('contact').value = visible ? '0' + visible : '';
                currentStep = 2;
                showStep(2);
                return;
            }

            // Validate step2 before allowing submission
            if (!validateStep2()) {
                e.preventDefault();
                return;
            }

            // Ensure contact hidden field is populated (final guard)
            const visible = document.getElementById('contactVisible').value.replace(/\D/g, '').slice(0, 10);
            document.getElementById('contact').value = visible ? '0' + visible : '';
        });

        // Password toggle helpers — consistent icon state
        function togglePassword(buttonId, inputId, iconId) {
            const btn = document.getElementById(buttonId);
            if (!btn) return;
            btn.addEventListener('click', function() {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';

                // reset classes then set correct icon
                icon.classList.remove('bi-eye-fill', 'bi-eye-slash-fill');
                icon.classList.add(isHidden ? 'bi-eye-fill' : 'bi-eye-slash-fill');

                // accessibility: update aria-pressed and aria-label
                btn.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
                btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            });
        }
        togglePassword('togglePassword', 'password', 'toggleIcon1');
        togglePassword('toggleConfirmPassword', 'confirmPassword', 'toggleIcon2');

        // Contact input: only digits, max 10 visible digits (after +63). sanitize on paste & input
        const contactVisible = document.getElementById('contactVisible');
        if (contactVisible) {
            contactVisible.addEventListener('keydown', function(e) {
                // Allow: backspace, delete, tab, escape, enter, arrow keys and digits
                const allowedKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'];
                if (allowedKeys.includes(e.key) || (e.key >= '0' && e.key <= '9')) {
                    return;
                }
                e.preventDefault();
            });

            contactVisible.addEventListener('paste', function(e) {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text');
                const digits = text.replace(/\D/g, '').slice(0, 10);
                this.value = digits;
            });

            contactVisible.addEventListener('input', function() {
                const digits = this.value.replace(/\D/g, '').slice(0, 10);
                if (this.value !== digits) this.value = digits;
                this.classList.remove('is-invalid');
            });
        }

        // Address dropdowns: load JSONs and populate selects (store names in hidden inputs)
        document.addEventListener('DOMContentLoaded', function() {
            showStep(currentStep); // initialize UI

            const provinceSelect = document.getElementById('provinceSelect');
            const citySelect = document.getElementById('citySelect');
            const barangaySelect = document.getElementById('barangaySelect');

            const provinceInput = document.getElementById('province');
            const cityInput = document.getElementById('city');
            const barangayInput = document.getElementById('barangay');

            let provinces = [],
                cities = [],
                barangays = [];

            const files = [
                '../assets/json/province.json',
                '../assets/json/city.json',
                '../assets/json/barangay.json'
            ];

            Promise.all(files.map(url => fetch(url).then(resp => {
                if (!resp.ok) throw new Error(`Failed to load ${url}: ${resp.statusText}`);
                return resp.json();
            }))).then(data => {
                provinces = data[0];
                cities = data[1];
                barangays = data[2];

                populateDropdown(provinceSelect, provinces, 'province_code', 'province_name');
            }).catch(err => {
                console.error("Error loading address data:", err);
                // non-blocking: leave the selects with their default option
            });

            function populateDropdown(dropdown, data, valueKey, textKey) {
                dropdown.innerHTML = '<option value="" selected disabled>Select Option</option>';
                dropdown.disabled = false;
                data.sort((a, b) => a[textKey].localeCompare(b[textKey]));
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item[valueKey];
                    option.textContent = item[textKey];
                    option.dataset.name = item[textKey];
                    dropdown.appendChild(option);
                });
            }

            function resetDropdown(dropdown, placeholder) {
                dropdown.innerHTML = `<option value="" selected disabled>${placeholder}</option>`;
                dropdown.disabled = true;
            }

            if (provinceSelect) {
                provinceSelect.addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    const code = this.value;
                    const name = selected.dataset.name || selected.textContent;
                    if (provinceInput) provinceInput.value = name;

                    const filteredCities = cities.filter(c => c.province_code === code);
                    populateDropdown(citySelect, filteredCities, 'city_code', 'city_name');

                    // reset below fields
                    if (cityInput) cityInput.value = '';
                    if (barangayInput) barangayInput.value = '';
                    resetDropdown(barangaySelect, "Select City first");
                });
            }

            if (citySelect) {
                citySelect.addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    const code = this.value;
                    const name = selected.dataset.name || selected.textContent;
                    if (cityInput) cityInput.value = name;

                    const filteredBarangays = barangays.filter(b => b.city_code === code);
                    populateDropdown(barangaySelect, filteredBarangays, 'brgy_code', 'brgy_name');

                    if (barangayInput) barangayInput.value = '';
                });
            }

            if (barangaySelect) {
                barangaySelect.addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    const name = selected.dataset.name || selected.textContent;
                    if (barangayInput) barangayInput.value = name;
                });
            }
        });

        // Remove invalid state when user edits fields
        document.querySelectorAll('input, select').forEach(el => {
            el.addEventListener('input', () => el.classList.remove('is-invalid'));
            el.addEventListener('change', () => el.classList.remove('is-invalid'));
        });
    </script>
</body>

</html>