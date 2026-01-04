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
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
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
                            <h2 style="margin:0 0 10px;">Welcome to PackIT, ' . htmlspecialchars($first_name) . '!</h2>
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Join PackIT - Driver Partner Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --packit-yellow: #fce354;
            --packit-blue: #fce354;
        }

        body {
            background-color: #f8f9fa;
        }

        .gradient-banner {
            background: linear-gradient(135deg, #f5f568 0%, #eff96c 100%);
            height: 20px;
        }

        .dot {
            height: 12px;
            width: 12px;
            margin: 0 4px;
            background-color: #ddd;
            border-radius: 50%;
            display: inline-block;
            cursor: pointer;
            transition: 0.3s;
        }

        .dot.active {
            background-color: var(--packit-blue);
            transform: scale(1.2);
        }

        .step-content {
            min-height: 450px;
            animation: fadeIn 0.5s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--packit-yellow);
            box-shadow: 0 0 0 0.25rem rgba(252, 227, 84, 0.25);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .card {
            border: none !important;
        }

        .is-invalid {
            border-color: #dc3545 !important;
            background-image: none;
        }

        .is-invalid:focus {
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
        }

        .form-control,
        .form-select {
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
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

            <div class="col-12 col-lg-5 p-5 text-center d-flex flex-column justify-content-center align-items-center bg-white shadow-sm">
                <img src="../assets/packit-poster.svg" class="img-fluid mb-3" alt="PackIT Driver"
                    style="max-height:450px; width:auto; object-fit:contain;">
                <h2 class="fw-bold text-primary">PackIT Driver</h2>
                <p class="text-muted lead">Your Journey, Your Earnings. Start Delivering Today.</p>
            </div>

            <div class="col-12 col-lg-7 p-lg-5">
                <div class="d-flex justify-content-center">
                    <div class="card border-0 rounded-5 shadow-lg" style="max-width:750px; width:100%;">
                        <div class="card-body p-4 p-md-5">

                            <h1 class="text-center fw-bold mb-2">Driver Account</h1>
                            <p class="text-center text-muted mb-5">Join the fleet and start earning</p>

                            <?php if ($error): ?>
                                <div class="alert alert-danger mb-4"><?= $error ?></div>
                            <?php endif; ?>

                            <form action="signup.php" method="POST" id="DriverProfile">

                                <div id="step1" class="step-content">
                                    <div class="d-flex align-items-baseline mb-4 border-bottom pb-2">
                                        <h3 class="mb-0" style="color: var(--packit-yellow);"><i class="bi bi-person-circle"></i> Profile & Security</h3>
                                        <small class="ms-auto text-muted">*Required</small>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">First Name *</label>
                                            <input type="text" class="form-control" name="first_name" placeholder="e.g. Juan" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Last Name *</label>
                                            <input type="text" class="form-control" name="last_name" placeholder="e.g. Dela Cruz" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email Address *</label>
                                            <input type="email" class="form-control" name="email" placeholder="driver@packit.com" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Contact Number *</label>
                                            <input type="tel" class="form-control" name="contact" placeholder="09XXXXXXXXX" required>
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
                                            <small class="text-muted d-block mt-1 ms-2 fw-normal" style="font-size:0.8rem;">
                                                Must be at least 8 characters with letters and numbers.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <label for="confirm_password" class="form-label">Confirm Password *</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="confirm_password"
                                                    name="confirm_password" placeholder="Re-enter password" required>
                                                <button type="button" id="toggleConfirmPassword" class="btn btn-outline-secondary">
                                                    <i id="toggleIcon2" class="bi bi-eye-fill"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="step2" class="step-content" style="display:none;">
                                    <div class="d-flex align-items-baseline mb-4 border-bottom pb-2">
                                        <h3 class="text-primary mb-0"><i class="bi bi-truck"></i> Vehicle & Address</h3>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Vehicle Type *</label>
                                            <select name="vehicle_type" class="form-select" required>
                                                <option value="" disabled selected>Select Vehicle</option>
                                                <option value="Motorcycle">Motorcycle</option>
                                                <option value="Tricycle">Tricycle</option>
                                                <option value="Sedan">Sedan</option>
                                                <option value="Pick-up Truck">Pick-up Truck</option>
                                                <option value="Closed Van">Closed Van</option>
                                                <option value="Forward Truck">Forward Truck</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">License Plate *</label>
                                            <input type="text" class="form-control" name="license_plate" placeholder="ABC 1234" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">House/Unit No. *</label>
                                            <input type="text" class="form-control" name="house_num" placeholder="#123" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Street *</label>
                                            <input type="text" class="form-control" name="street" placeholder="Maple St." required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <label for="provinceSelect" class="form-label">Province *</label>
                                            <select class="form-select" id="provinceSelect" required>
                                                <option value="" selected disabled>Loading...</option>
                                            </select>
                                            <input type="hidden" id="province" name="province" value="">
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <label for="citySelect" class="form-label">City/Municipality *</label>
                                            <select class="form-select" id="citySelect" disabled required>
                                                <option value="" selected disabled>Select Province first</option>
                                            </select>
                                            <input type="hidden" id="city" name="city" value="">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <label for="barangaySelect" class="form-label">Barangay *</label>
                                            <select class="form-select" id="barangaySelect" disabled required>
                                                <option value="" selected disabled>Select City first</option>
                                            </select>
                                            <input type="hidden" id="barangay" name="barangay" value="">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="postal" class="form-label">Zip Code</label>
                                            <input type="text" class="form-control" id="postal" name="postal" placeholder="4234">
                                        </div>
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

                                    <button type="button" id="nextBtn" class="btn btn-primary px-4 py-2 ms-auto">
                                        Next <i class="bi bi-arrow-right"></i>
                                    </button>

                                    <button type="submit" id="submitBtn" class="btn btn-warning px-5 py-2 fw-bold ms-auto" style="display:none;">
                                        CREATE DRIVER ACCOUNT
                                    </button>
                                </div>

                                <p class="text-center mt-4 small text-muted">
                                    By registering, you agree to our <a href="#">Driver Partner Terms</a>.
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let currentStep = 1;

        function showStep(step) {
            document.getElementById('step1').style.display = step === 1 ? 'block' : 'none';
            document.getElementById('step2').style.display = step === 2 ? 'block' : 'none';

            document.querySelectorAll('.dot').forEach((dot, idx) => {
                dot.classList.toggle('active', (idx + 1) === step);
            });

            document.getElementById('backBtn').style.display = step === 1 ? 'none' : 'block';
            document.getElementById('nextBtn').style.display = step === 1 ? 'block' : 'none';
            document.getElementById('submitBtn').style.display = step === 2 ? 'block' : 'none';
        }

        function validateStepFields(stepId) {
            const container = document.getElementById(stepId);
            const inputs = container.querySelectorAll('input[required], select[required]');
            let isStepValid = true;

            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    input.classList.add('is-invalid');
                    isStepValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isStepValid) {
                container.querySelector('.is-invalid').focus();
            }
            return isStepValid;
        }

        document.getElementById('nextBtn').addEventListener('click', () => {
            if (validateStepFields('step1')) {
                currentStep = 2;
                showStep(2);
            }
        });

        document.getElementById('backBtn').addEventListener('click', () => {
            currentStep = 1;
            showStep(1);
        });

        document.getElementById('DriverProfile').addEventListener('submit', function(e) {
            if (!validateStepFields('step2')) {
                e.preventDefault();
            }
        });

        function setupToggle(buttonId, inputId, iconId) {
            const btn = document.getElementById(buttonId);
            if (btn) {
                btn.addEventListener('click', function() {
                    const input = document.getElementById(inputId);
                    const icon = document.getElementById(iconId);
                    const isPass = input.type === 'password';
                    input.type = isPass ? 'text' : 'password';
                    icon.classList.toggle('bi-eye-fill', !isPass);
                    icon.classList.toggle('bi-eye-slash-fill', isPass);
                });
            }
        }
        setupToggle('togglePassword', 'password', 'toggleIcon1');
        setupToggle('toggleConfirmPassword', 'confirm_password', 'toggleIcon2');

        document.addEventListener("DOMContentLoaded", () => {
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

            Promise.all(files.map(url => fetch(url).then(r => r.json())))
                .then(data => {
                    provinces = data[0];
                    cities = data[1];
                    barangays = data[2];
                    populateDropdown(provinceSelect, provinces, 'province_code', 'province_name');
                })
                .catch(err => console.error("Data Load Error:", err));

            function populateDropdown(dropdown, data, valueKey, textKey) {
                dropdown.innerHTML = '<option value="" selected disabled>Select Option</option>';
                dropdown.disabled = false;
                data.sort((a, b) => a[textKey].localeCompare(b[textKey]));
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item[valueKey];
                    opt.textContent = item[textKey];
                    opt.dataset.name = item[textKey];
                    dropdown.appendChild(opt);
                });
            }

            provinceSelect.addEventListener('change', function() {
                provinceInput.value = this.options[this.selectedIndex].dataset.name;
                const filtered = cities.filter(c => c.province_code === this.value);
                populateDropdown(citySelect, filtered, 'city_code', 'city_name');
            });

            citySelect.addEventListener('change', function() {
                cityInput.value = this.options[this.selectedIndex].dataset.name;
                const filtered = barangays.filter(b => b.city_code === this.value);
                populateDropdown(barangaySelect, filtered, 'brgy_code', 'brgy_name');
            });

            barangaySelect.addEventListener('change', function() {
                barangayInput.value = this.options[this.selectedIndex].dataset.name;
            });
        });
    </script>
</body>

</html>