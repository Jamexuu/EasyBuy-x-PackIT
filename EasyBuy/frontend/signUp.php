<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up</title>
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
        input:focus,
        textarea:focus,
        select:focus {
            box-shadow: 0 0 0 0.05rem #6EC064 !important;
            outline: none !important;
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
                <div class="card mx-3 p-5 rounded-4">
               
                    <div id="step1" class="step-content">
                        <div class="d-flex align-items-baseline mb-4">
                            <h3 class="text-start mb-0 me-2">Profile</h3>
                            <small class="text-muted fw-normal">*Required Fields have asterisks</small>
                        </div>
                        <form>
                        <div class="row mb-3">
                            <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                <label for="lastName" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="lastName" placeholder="Enter Last Name">
                            </div>
                            <div class="col-12 col-lg-6">
                                <label for="firstName" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="firstName" placeholder="Enter First Name">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                <label for="contact" class="form-label">Contact Number *</label>
                                <input type="tel" class="form-control" id="contact" placeholder="Enter Contact Number">
                            </div>
                            <div class="col-12 col-lg-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" placeholder="Enter Email">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="password" class="form-label">Password *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password"
                                        placeholder="Enter Password">
                                    <button type="button" id="togglePassword" class="btn btn-outline-secondary">
                                        <i id="toggleIcon1" class="bi bi-eye-fill"></i>
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1 ms-2 fw-normal">Use at least 15 alphanumeric characters and
                                    symbols</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label for="confirm_password" class="form-label">Confirm Password *
                                    <button type="button" class="btn btn-link btn-sm p-0 ms-1" data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="For verification, enter the same password again.">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password"
                                        placeholder="Confirm Password">
                                    <button type="button" id="toggleConfirmPassword" class="btn btn-outline-secondary">
                                        <i id="toggleIcon2" class="bi bi-eye-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div id="step2" class="step-content" style="display: none;">
                    <h3 class="text-start mb-4">Address</h3>
                    <form>
                        <div class="row mb-3">
                            <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                <label for="houseNumber" class="form-label">House Number *</label>
                                <input type="text" class="form-control" id="houseNumber" placeholder="#7">
                            </div>
                            <div class="col-12 col-lg-6">
                                <label for="street" class="form-label">Street *</label>
                                <input type="text" class="form-control" id="street" placeholder="Zone - 1">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                <label for="lot" class="form-label">Lot</label>
                                <input type="text" class="form-control" id="lot" placeholder="">
                            </div>
                            <div class="col-12 col-lg-6">
                                <label for="block" class="form-label">Block</label>
                                <input type="text" class="form-control" id="block" placeholder="">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                <label for="barangay" class="form-label">Barangay *</label>
                                <input type="text" class="form-control" id="barangay" placeholder="Altura Bata">
                            </div>
                            <div class="col-12 col-lg-6">
                                <label for="city" class="form-label">City *</label>
                                <input type="text" class="form-control" id="city" placeholder="Tanauan City">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                <label for="province" class="form-label">Province *</label>
                                <input type="text" class="form-control" id="province" placeholder="Batangas">
                            </div>
                            <div class="col-12 col-lg-6">
                                <label for="postal" class="form-label">Postal</label>
                                <input type="text" class="form-control" id="postal" placeholder="4232">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center my-4">
                <span class="dot active" data-step="1"></span>
                <span class="dot" data-step="2"></span>
            </div>

            <div class="d-flex justify-content-between mx-3">
                <button type="button" id="backBtn" class="btn btn-success px-4 py-2" style="display: none;">BACK</button>
                <button type="button" id="nextBtn" class="btn btn-success px-4 py-2 ms-auto">NEXT</button>
                <button type="button" id="submitBtn" class="btn btn-success px-4 py-2 ms-auto" style="display: none;">SUBMIT</button>
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

        document.getElementById('nextBtn').addEventListener('click', function() {
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

        document.getElementById('submitBtn').addEventListener('click', function() {
            alert('Form submitted!');
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
    </script>
</body>

</html>