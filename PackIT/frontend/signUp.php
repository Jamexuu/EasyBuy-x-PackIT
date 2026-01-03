<! doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Join PackIT - Delivery Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --packit-blue:#0d6efd;
            --packit-dark:#0a58ca;
        }

        body {
            background-color:#f8f9fa;
        }

        .gradient-banner {
            background:linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            height:100%;
            min-height:20px;
        }

        .dot {
            height:12px;
            width:12px;
            margin:0 4px;
            background-color:#ddd;
            border-radius:50%;
            display:inline-block;
            cursor:pointer;
            transition:background-color 0.3s;
        }

        .dot.active {
            background-color:var(--packit-blue);
            transform:scale(1.2);
        }

        .step-content {
            min-height:400px;
            animation:fadeIn 0.5s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color:var(--packit-blue);
            box-shadow:0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        @keyframes fadeIn {
            from { opacity:0; }
            to { opacity:1; }
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col">
                <div class="p-2 gradient-banner"></div>
            </div>
        </div>
    </div>

    <div class="container-fluid h-100">
        <div class="row h-100 align-items-center">

            <div class="col-12 col-lg-5 col-md-12 p-5 text-center d-flex flex-column justify-content-center align-items-center bg-white shadow-sm">
                <img src="../assets/packit-poster.svg" class="img-fluid mb-3" alt="PackIT Delivery Logo"
                    style="max-height:500px; width:auto; object-fit:contain;">

                <h2 class="fw-bold text-primary">PackIT</h2>
                <p class="text-muted lead">Fast.Reliable.Everywhere.</p>
            </div>

            <div class="col-12 col-lg-7 col-md-12 p-lg-5">
                <div class="d-flex justify-content-center">
                    <div class="card border-0 rounded-4" style="max-width:700px; width:100%;">
                        <div class="card-body p-4 p-md-5">

                            <h1 class="text-center fw-bold mb-2">Create Account</h1>
                            <p class="text-center text-muted mb-5">Start shipping with PackIT today</p>

                            <form action="signUpProcess.php" method="POST" id="signUpForm">

                                <div id="step1" class="step-content">
                                    <div class="d-flex align-items-baseline mb-4 border-bottom pb-2">
                                        <h3 class="text-start mb-0 me-2 text-primary"><i class="bi bi-person-badge"></i> Profile</h3>
                                        <small class="text-muted fw-normal ms-auto">*Required Fields</small>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <label for="firstName" class="form-label">First Name *</label>
                                            <input type="text" class="form-control" id="firstName" name="firstName"
                                                placeholder="e.g.Juan" required>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="lastName" class="form-label">Last Name *</label>
                                            <input type="text" class="form-control" id="lastName" name="lastName"
                                                placeholder="e.g.Dela Cruz" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <label for="contact" class="form-label">Mobile Number *</label>
                                            <input type="tel" class="form-control" id="contact" name="contact"
                                                placeholder="0912 345 6789" required>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="email" class="form-label">Email Address *</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="name@example.com" required>
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
                                        <h3 class="text-start mb-0 me-2 text-primary"><i class="bi bi-geo-alt-fill"></i> Main Address</h3>
                                        <small class="text-muted fw-normal ms-auto">For pickups & deliveries</small>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <label for="houseNumber" class="form-label">House/Unit No.*</label>
                                            <input type="text" class="form-control" id="houseNumber"
                                                name="houseNumber" placeholder="#42 or Unit 101" required>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="street" class="form-label">Street Name *</label>
                                            <input type="text" class="form-control" id="street" name="street"
                                                placeholder="Main Avenue" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <label for="subdivision" class="form-label">Subdivision/Village</label>
                                            <input type="text" class="form-control" id="subdivision" name="subdivision"
                                                placeholder="Greenfield Estate">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="landmark" class="form-label">Nearest Landmark (Optional)</label>
                                            <input type="text" class="form-control" id="landmark" name="landmark"
                                                placeholder="Near the Blue Gate">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <label for="provinceSelect" class="form-label">Province *</label>
                                            <select class="form-select" id="provinceSelect" required>
                                                <option value="" selected disabled>Loading...</option>
                                            </select>
                                            <!-- Hidden field to store the province NAME -->
                                            <input type="hidden" id="province" name="province" value="">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="citySelect" class="form-label">City/Municipality *</label>
                                            <select class="form-select" id="citySelect" disabled required>
                                                <option value="" selected disabled>Select Province first</option>
                                            </select>
                                            <!-- Hidden field to store the city NAME -->
                                            <input type="hidden" id="city" name="city" value="">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                            <label for="barangaySelect" class="form-label">Barangay *</label>
                                            <select class="form-select" id="barangaySelect" disabled required>
                                                <option value="" selected disabled>Select City first</option>
                                            </select>
                                            <!-- Hidden field to store the barangay NAME -->
                                            <input type="hidden" id="barangay" name="barangay" value="">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label for="postal" class="form-label">Zip Code</label>
                                            <input type="text" class="form-control" id="postal" name="postal"
                                                placeholder="4234">
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

                                    <button type="button" id="nextBtn" class="btn btn-primary px-4 py-2 ms-auto">
                                        Next <i class="bi bi-arrow-right"></i>
                                    </button>

                                    <button type="submit" id="submitBtn" class="btn btn-primary px-5 py-2 ms-auto"
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
        // --- 1.Stepper Logic ---
        var currentStep = 1;
        const totalSteps = 2;

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

            if (step === 1) {
                backBtn.style.display = 'none';
                nextBtn.style.display = 'block';
                submitBtn.style.display = 'none';
            } else if (step === totalSteps) {
                backBtn.style.display = 'block';
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'block';
            }
        }

        document.getElementById('nextBtn').addEventListener('click', function () {
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

        // --- 2.Password Toggle Logic ---
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon1');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' :'password';
            icon.classList.toggle('bi-eye-fill', ! isHidden);
            icon.classList.toggle('bi-eye-slash-fill', isHidden);
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
            const input = document.getElementById('confirm_password');
            const icon = document.getElementById('toggleIcon2');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' :'password';
            icon.classList.toggle('bi-eye-fill', !isHidden);
            icon.classList.toggle('bi-eye-slash-fill', isHidden);
        });

        // --- 3.JSON Address Dropdown Logic (FIXED - saves names, not codes) ---
        document.addEventListener("DOMContentLoaded", () => {
            const provinceSelect = document.getElementById('provinceSelect');
            const citySelect = document.getElementById('citySelect');
            const barangaySelect = document.getElementById('barangaySelect');
            
            // Hidden fields to store the actual names
            const provinceInput = document.getElementById('province');
            const cityInput = document.getElementById('city');
            const barangayInput = document.getElementById('barangay');

            let provinces = [], cities = [], barangays = [];

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
                    alert("Error:Could not load location data.Please check the Console (F12) for file path errors.");
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

            // --- Event Listeners (FIXED - now saves names to hidden fields) ---
            provinceSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const selectedProvinceCode = this.value;
                const selectedProvinceName = selectedOption.dataset.name || selectedOption.textContent;
                
                // Save the NAME to hidden field
                provinceInput.value = selectedProvinceName;
                
                const filteredCities = cities.filter(c => c.province_code === selectedProvinceCode);
                populateDropdown(citySelect, filteredCities, 'city_code', 'city_name');
                
                // Reset city and barangay
                cityInput.value = '';
                barangayInput.value = '';
                resetDropdown(barangaySelect, "Select City first");
            });

            citySelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const selectedCityCode = this.value;
                const selectedCityName = selectedOption.dataset.name || selectedOption.textContent;
                
                // Save the NAME to hidden field
                cityInput.value = selectedCityName;
                
                const filteredBarangays = barangays.filter(b => b.city_code === selectedCityCode);
                populateDropdown(barangaySelect, filteredBarangays, 'brgy_code', 'brgy_name');
                
                // Reset barangay
                barangayInput.value = '';
            });

            barangaySelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const selectedBarangayName = selectedOption.dataset.name || selectedOption.textContent;
                
                // Save the NAME to hidden field
                barangayInput.value = selectedBarangayName;
            });
        });
    </script>
</body>

</html>