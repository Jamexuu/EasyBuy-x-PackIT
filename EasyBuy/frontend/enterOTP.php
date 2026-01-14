<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EasyBuy - Enter OTP</title>
    <link rel="shortcut icon" href="../assets/easybuylogo.svg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
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
            <div class="col">
                <div class="h1 text-center">
                    Forgot your password?
                </div>
                <p class="text-center text-secondary">No worries! We sent you an OTP to your saved 
                    mobile number ending in ****1234 to reset your password</p>
            </div>
        </div>
        <div class="row mt-3 mb-5">
            <div class="col col-md-8 col-lg-6 mx-md-auto d-flex justify-content-center">
                <div style="max-width: 400px; width: 100%;">
                    <h5 class="text-center mb-4">Enter the 6-digit code.</h5>
                    
                    <div class="row justify-content-center mb-3">
                        <div class="col-12 col-md-auto d-flex justify-content-center gap-2 flex-wrap">
                            <input type="text" maxlength="1" class="otp-input text-center form-control" style="max-width: 50px; font-size: 24px; border-radius: 8px;" />
                            <input type="text" maxlength="1" class="otp-input text-center form-control" style="max-width: 50px; font-size: 24px; border-radius: 8px;" />
                            <input type="text" maxlength="1" class="otp-input text-center form-control" style="max-width: 50px; font-size: 24px; border-radius: 8px;" />
                            <span class="d-none d-md-inline" style="font-size: 24px; font-weight: bold; padding: 0 10px;">—</span>
                            <input type="text" maxlength="1" class="otp-input text-center form-control" style="max-width: 50px; font-size: 24px; border-radius: 8px;" />
                            <input type="text" maxlength="1" class="otp-input text-center form-control" style="max-width: 50px; font-size: 24px; border-radius: 8px;" />
                            <input type="text" maxlength="1" class="otp-input text-center form-control" style="max-width: 50px; font-size: 24px; border-radius: 8px;" />
                        </div>
                    </div>

                    <p class="text-danger text-center fw-normal small" id="otpError"></p>
                    
                    <p class="text-center mb-4">
                        Didn't receive one? <a href="#" id="resendCode" style="color: #6EC064; text-decoration: none; font-weight: 500;">Resend code.</a>
                    </p>
                    
                    <button type="button" id="submitOTP" class="btn w-100 text-white" style="background-color: #6EC064; padding: 12px; border-radius: 8px; font-weight: 500;">Submit code</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    
    <script>
        const otpInputs = document.querySelectorAll('.otp-input');
        const submitBtn = document.getElementById('submitOTP');
        const resendLink = document.getElementById('resendCode');
        const otpError = document.getElementById('otpError');

        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                if (this.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
                const inputs = document.querySelectorAll('.otp-input');
                
                for (let i = 0; i < pasteData.length && i < inputs.length; i++) {
                    inputs[i].value = pasteData[i];
                }
                
                if (pasteData.length >= 6) {
                    inputs[5].focus();
                }
            });
        });

        async function verifyOTP(otp) {
            try {
                const response = await fetch('../api/verifyForgotPasswordOTP.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ otp: otp })
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = 'createNewPassword.php';
                } else {
                    otpInputs.forEach(input => input.value = '');
                    otpInputs[0].focus();
                    otpInputs.forEach(input => input.style.border = '1px solid red');
                    otpError.textContent = result.error || 'Invalid OTP. Please try again.';
                }

            } catch (error) {
                console.error('Error verifying OTP:', error);
                return { success: false, error: 'An error occurred while verifying the OTP.' };
            }
        }

        document.getElementById('submitOTP').addEventListener('click', function() {
            const otpInputs = document.querySelectorAll('.otp-input');
            const otp = Array.from(otpInputs).map(input => input.value).join('');
            
            if (otp.length !== 6) {
                otpError.textContent = 'Please enter the complete 6-digit OTP.';
                return;
            }
            verifyOTP(otp);
            
        });

        document.getElementById('resendCode').addEventListener('click', function(e) {
            e.preventDefault();
            // Add your resend OTP logic here
            alert('Resending OTP...');
        });
    </script>
