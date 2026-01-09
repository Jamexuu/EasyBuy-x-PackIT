<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Join PackIT - Delivery Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --packit-yellow: #f5d84b;
            --packit-blue: #f5d84b;
        }

        body {
            background-color: #f8f9fa;
        }

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
            background-color: var(--packit-yellow);
            transform: scale(1.2);
        }

        .step-content {
            min-height: 400px;
            animation: fadeIn 0.5s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--packit-yellow);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.08);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Header style to match the provided image */
        .section-header {
            display: flex;
            align-items: center;
            gap: .75rem;
            color: var(--packit-yellow);
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: .5rem;
        }

        .section-header .bi {
            background: rgba(245, 216, 75, 0.12);
            border-radius: 50%;
            padding: .25rem;
            font-size: 1.35rem;
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

        /* Inputs: rounded, subtle border, bigger padding like the image */
        .form-control {
            border-radius: .5rem;
            border: 1px solid #e6e9ee;
            padding: .75rem 1rem;
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

        /* Ensure input-group height matches plain .form-control */
        .input-group .form-control,
        .input-group .input-group-text {
            min-height: calc(1.5em + .75rem + 2px);
            display: flex;
            align-items: center;
            padding: .75rem 1rem;
        }

        /* Slight spacing for helper text to match the style */
        .form-text.small {
            margin-top: .25rem;
            color: #6c757d;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .section-header {
                font-size: 1.25rem;
            }

            .form-control {
                padding: .55rem .75rem;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid h-100">
        <div class="row h-100 align-items-center">

            <div class="col-12 py-5">
                <div class="d-flex justify-content-center">
                    <div class="card border-0 rounded-4" style="max-width:700px; width:1500px;">
                        <div class="card-body p-4 p-md-5">

                            <h1 class="text-center fw-bold mb-2">Create Account</h1>
                            <p class="text-center text-muted mb-5">Start shipping with PackIT today</p>

                            <form action="signUpProcess.php" method="POST" id="signUpForm" novalidate>



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
                                                    aria-describedby="contactFeedback">
                                            </div>
                                            <!-- hidden field to be submitted -->
                                            <input type="hidden" id="contact" name="contact" value="">
                                            <!-- Invalid feedback element (was missing) -->
                                            <div class="invalid-feedback" id="contactFeedback">Please enter exactly 10 digits after +63 (e.g. 9913389514).</div>
                                            <div class="form-text small">Do not type the leading 0 — enter the number after +63 (e.g. 9913389514).</div>
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
                                                <input type="password" class="form-control" id="confirm_password"
                                                    name="confirm_password" placeholder="Re-enter password" required>
                                                <button type="button" id="toggleConfirmPassword" class="btn btn-outline-secondary">
                                                    <i id="toggleIcon2" class="bi bi-eye-fill"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback" id="confirmFeedback">Passwords do not match.</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- STEP 2 (unchanged structure, preserved floating selects) -->


                                <div id="step2" class="step-content" style="display:none;">
                                    <div class="section-header mb-3">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        <span>Main &amp; Address</span>
                                        <small class="text-muted fw-normal ms-auto fs-6">For pickups & deliveries</small>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <label for="houseNumber" class="form-label">House/Unit No.*</label>
                                            <input type="text" class="form-control" id="houseNumber"
                                                name="houseNumber" placeholder="e.g. #42 or Unit 101" required>
                                            <div class="invalid-feedback">Please enter a house or unit number.</div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="street" class="form-label">Street Name *</label>
                                            <input type="text" class="form-control" id="street" name="street"
                                                placeholder="e.g. Main Avenue" required>
                                            <div class="invalid-feedback">Please enter a street name.</div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <label for="subdivision" class="form-label">Subdivision/Village</label>
                                            <input type="text" class="form-control" id="subdivision" name="subdivision"
                                                placeholder="e.g. Greenfield Estate">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="landmark" class="form-label">Nearest Landmark (Optional)</label>
                                            <input type="text" class="form-control" id="landmark" name="landmark"
                                                placeholder="e.g. Near the Blue Gate">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <div class="form-floating">
                                                <select class="form-select" id="provinceSelect" required>
                                                    <option value="" selected disabled>Loading...</option>
                                                </select>
                                                <label for="provinceSelect">Province *</label>
                                                <div class="invalid-feedback">Please choose a province.</div>
                                            </div>
                                            <!-- Hidden field to store the province NAME (only one, kept here) -->
                                            <input type="hidden" id="province" name="province" value="">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="form-floating">
                                                <select class="form-select" id="citySelect" disabled required>
                                                    <option value="" selected disabled>Select Province first</option>
                                                </select>
                                                <label for="citySelect">City/Municipality *</label>
                                                <div class="invalid-feedback">Please choose a city/municipality.</div>
                                            </div>
                                            <input type="hidden" id="city" name="city" value="">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <div class="form-floating">
                                                <select class="form-select" id="barangaySelect" disabled required>
                                                    <option value="" selected disabled>Select City first</option>
                                                </select>
                                                <label for="barangaySelect">Barangay *</label>
                                                <div class="invalid-feedback">Please choose a barangay.</div>
                                            </div>
                                            <input type="hidden" id="barangay" name="barangay" value="">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="postal" class="form-label">Zip Code</label>
                                            <input type="text" class="form-control" id="postal" name="postal"
                                                placeholder="e.g. 4234">
                                        </div>
                                    </div>

                                    <div class="mt-4 p-3 bg-light rounded text-center">
                                        <p class="mb-0 small text-muted">
                                            By clicking submit, you agree to PackIT's <a href="#">Terms of Service</a>
                                            and <a href="#">Privacy Policy</a>.
                                        </p>
                                    </div>
                                </div>

                                <div class="text-center my-4">
                                    <span class="dot active" data-step="1"></span>
                                    <span class="dot" data-step="2"></span>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="button" id="backBtn" class="btn btn-outline-secondary px-4 py-2"
                                        style="display:none;">
                                        <i class="bi bi-arrow-left"></i> Back
                                    </button>

                                    <button type="button" id="nextBtn" class="btn btn-warning px-4 py-2 ms-auto">
                                        Next <i class="bi bi-arrow-right"></i>
                                    </button>

                                    <button type="submit" id="submitBtn" class="btn btn-warning px-5 py-2 ms-auto"
                                        style="display:none;">
                                        Create Account
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // --- Stepper Logic + validation before advancing ---
        var currentStep = 1;
        const totalSteps = 2;

        const form = document.getElementById('signUpForm');

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


        function showStep(step) {
            document.querySelectorAll('.step-content').forEach(el => el.style.display = 'none');
            const currentSection = document.getElementById(`step${step}`);
            currentSection.style.display = 'block';

            document.querySelectorAll('.dot').forEach(dot => {
                dot.classList.remove('active');
                if (parseInt(dot.dataset.step) === step) {
                    dot.classList.add('active');
                }
            });

            const backBtn = document.getElementById('backBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');

            // Hide the profile header when user is in step 2 (Main Address)
            const profileHeader = document.getElementById('profileHeader');
            if (profileHeader) {
                profileHeader.style.display = (step === 1) ? 'flex' : 'none';
            }

            if (step === 1) {
                backBtn.style.display = 'none';
                nextBtn.style.display = 'block';
                submitBtn.style.display = 'none';
            } else if (step === totalSteps) {
                backBtn.style.display = 'block';
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'block';
            }

            // scroll to top of card on step change (mobile-friendly)
            currentSection.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }

        document.getElementById('nextBtn').addEventListener('click', function() {
            if (currentStep < totalSteps) {
                if (currentStep === 1) {
                    if (!validateStep1()) return;
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

        // --- Password Toggle Logic ---
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon1');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye-fill', !isHidden);
            icon.classList.toggle('bi-eye-slash-fill', isHidden);
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const input = document.getElementById('confirm_password');
            const icon = document.getElementById('toggleIcon2');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye-fill', !isHidden);
            icon.classList.toggle('bi-eye-slash-fill', isHidden);
        });

        // --- Contact input: only digits, max 10 visible digits (after +63).
        // --- Prevent leading 0 in the visible field; on submit we'll store the '0'-prefixed value in the hidden field.
        const contactVisible = document.getElementById('contactVisible');
        const contactHidden = document.getElementById('contact');
        const contactFeedback = document.getElementById('contactFeedback');

        // Keydown: allow control/navigation, digits, block leading zero typing at caret position 0 (no selection)
        contactVisible.addEventListener('keydown', function(e) {
            const allowedKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'Home', 'End',
                'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'
            ];

            // allow navigation/control keys and clipboard shortcuts
            if (allowedKeys.includes(e.key) || e.ctrlKey || e.metaKey) return;

            // allow digits but block typing '0' at start when there's no selection
            if (e.key >= '0' && e.key <= '9') {
                const selStart = this.selectionStart ?? 0;
                const selEnd = this.selectionEnd ?? 0;

                // block leading '0' when inserting at position 0 with no selection
                if (e.key === '0' && selStart === 0 && selEnd === selStart) {
                    e.preventDefault();
                    return;
                }

                // enforce max 10 digits if no selection replacing digits
                const currDigits = this.value.replace(/\D/g, '');
                const selectionLength = selEnd - selStart;
                if (currDigits.length - selectionLength >= 10 && selectionLength === 0) {
                    e.preventDefault();
                    return;
                }

                return;
            }

            // block everything else
            e.preventDefault();
        });

        // On paste: strip non-digits, remove leading zeros, limit to 10 digits
        contactVisible.addEventListener('paste', function(e) {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text');
            let digits = text.replace(/\D/g, '');
            digits = digits.replace(/^0+/, ''); // remove leading zeros
            digits = digits.slice(0, 10);
            // insert at caret position respecting selection
            const selStart = this.selectionStart ?? 0;
            const selEnd = this.selectionEnd ?? 0;
            const before = this.value.slice(0, selStart).replace(/\D/g, '');
            const after = this.value.slice(selEnd).replace(/\D/g, '');
            let newDigits = (before + digits + after).slice(0, 10);
            // ensure no leading zero
            newDigits = newDigits.replace(/^0+/, '');
            this.value = newDigits;
        });

        // Input guard: ensure only digits, remove leading zeros and trim to 10
        contactVisible.addEventListener('input', function() {
            let digits = this.value.replace(/\D/g, '').slice(0, 10);
            digits = digits.replace(/^0+/, ''); // remove leading zeros for visible input
            if (this.value !== digits) this.value = digits;
            this.classList.remove('is-invalid');
        });

        // --- JSON Address Dropdown Logic (saves names, not codes) ---
        document.addEventListener("DOMContentLoaded", () => {
            const provinceSelect = document.getElementById('provinceSelect');
            const citySelect = document.getElementById('citySelect');
            const barangaySelect = document.getElementById('barangaySelect');

            // Hidden fields to store the actual names
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
                    if (!resp.ok) throw new Error(`Failed to load ${url}:${resp.statusText}`);
                    return resp.json();
                })))
                .then(data => {
                    provinces = data[0];
                    cities = data[1];
                    barangays = data[2];

                    console.log("JSON Data Loaded Successfully");
                    populateDropdown(provinceSelect, provinces, 'province_code', 'province_name');
                })
                .catch(err => {
                    console.error("Error loading address data:", err);
                    alert("Error: Could not load location data. Please check the Console (F12) for file path errors.");
                });

            function populateDropdown(dropdown, data, valueKey, textKey) {
                dropdown.innerHTML = '<option value="" selected disabled>Select Option</option>';
                dropdown.disabled = false;

                data.sort((a, b) => a[textKey].localeCompare(b[textKey]));

                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item[valueKey];
                    option.textContent = item[textKey];
                    option.dataset.name = item[textKey]; // Store the name in data attribute
                    dropdown.appendChild(option);
                });
            }

            function resetDropdown(dropdown, placeholder) {
                dropdown.innerHTML = `<option value="" selected disabled>${placeholder}</option>`;
                dropdown.disabled = true;
            }

            provinceSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const selectedProvinceCode = this.value;
                const selectedProvinceName = selectedOption.dataset.name || selectedOption.textContent;

                provinceInput.value = selectedProvinceName;

                const filteredCities = cities.filter(c => c.province_code === selectedProvinceCode);
                populateDropdown(citySelect, filteredCities, 'city_code', 'city_name');

                // Reset city and barangay
                cityInput.value = '';
                barangayInput.value = '';
                resetDropdown(barangaySelect, "Select City first");
            });

            citySelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const selectedCityCode = this.value;
                const selectedCityName = selectedOption.dataset.name || selectedOption.textContent;

                cityInput.value = selectedCityName;

                const filteredBarangays = barangays.filter(b => b.city_code === selectedCityCode);
                populateDropdown(barangaySelect, filteredBarangays, 'brgy_code', 'brgy_name');

                // Reset barangay
                barangayInput.value = '';
            });

            barangaySelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const selectedBarangayName = selectedOption.dataset.name || selectedOption.textContent;

                barangayInput.value = selectedBarangayName;
            });

            // initialize UI
            showStep(currentStep);
        });

        // --- Validation functions ---
        function validateStep1() {
            let valid = true;

            // simple HTML5 validity for built-in checks
            const firstName = document.getElementById('firstName');
            const lastName = document.getElementById('lastName');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirm = document.getElementById('confirm_password');

            [firstName, lastName, email, password, confirm].forEach(el => {
                el.classList.remove('is-invalid');
            });

            if (!firstName.value.trim()) {
                firstName.classList.add('is-invalid');
                valid = false;
            }
            if (!lastName.value.trim()) {
                lastName.classList.add('is-invalid');
                valid = false;
            }
            // email: rely on HTML5
            if (!email.checkValidity()) {
                email.classList.add('is-invalid');
                valid = false;
            }

            // contact: must be exactly 10 digits in visible input
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

            return valid;
        }

        function validateStep2() {
            let valid = true;
            const houseNumber = document.getElementById('houseNumber');
            const street = document.getElementById('street');
            const provinceSelect = document.getElementById('provinceSelect');
            const citySelect = document.getElementById('citySelect');
            const barangaySelect = document.getElementById('barangaySelect');

            [houseNumber, street, provinceSelect, citySelect, barangaySelect].forEach(el => {
                el.classList.remove('is-invalid');
            });

            if (!houseNumber.value.trim()) {
                houseNumber.classList.add('is-invalid');
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

            return valid;
        }

        // --- Final submit handler: validate step2, set contact hidden value to local format start with 0 ---
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // If user is still on step 1, validate and show step2
            if (currentStep === 1) {
                if (!validateStep1()) return;
                currentStep = 2;
                showStep(currentStep);
                return;
            }

            // Validate step2
            if (!validateStep2()) {
                // if invalid, scroll to first invalid
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return;
            }

            // Prepare contact hidden field: convert +63 9913... -> 0 + 9913...
            const visible = contactVisible.value.replace(/\D/g, '').slice(0, 10);
            contactHidden.value = visible ? '0' + visible : '';

            // Now submit normally
            form.submit();
        });

        // Remove invalid state when user types/corrects
        document.querySelectorAll('input, select').forEach(el => {
            el.addEventListener('input', () => {
                el.classList.remove('is-invalid');
            });
            el.addEventListener('change', () => {
                el.classList.remove('is-invalid');
            });
        });
    </script>
</body>

</html>