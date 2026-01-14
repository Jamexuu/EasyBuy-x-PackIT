<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EasyBuy - Enter Email for Password Reset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet">
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col">
                <div class="py-5 p-lg-5 gradient-banner" style="background: var(--gradient-color);"></div>
            </div>
        </div>
    </div>

    <button class="back-btn mx-3 my-3" onclick="window.history.back()"
        style="background: none; border: none; color: #6EC064; font-size: 2rem; cursor: pointer;">
        <span class="material-symbols-rounded">arrow_back</span>
    </button>

    <div class="container">
        <div class="row">
            <div class="col d-flex justify-content-center align-items-center">
                <img src="../assets/easybuylogo.svg" alt="Badge" class="img-fluid"
                    style="height: 370px; object-fit: cover;">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col col-md-8 col-lg-6 mx-md-auto text-center">
                <form method="POST" action="" onsubmit="sendOTP(event); return false;">
                    <div class="mb-3">
                        <label class="form-label" for="Email">Enter your registered email address</label>
                        <input type="email" id="Email" placeholder="Email" name="Email"
                            class="form-control py-2 rounded-3">
                    </div>
                    <div class="small text-danger mb-3 fw-normal" id="emailError"></div>
                    <button type="submit" class="btn btn-success px-4 py-2">Submit</button>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        const email = document.getElementById('Email');
        const emailError = document.getElementById('emailError');

        async function sendOTP() {
            try {

                if (!email.value) {
                    email.style.border = '1px solid red';
                    email.focus();
                    emailError.textContent = 'Please enter your email address.';
                    return;
                }

                const response = await fetch('../api/sendForgotPasswordOTP.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        email: email.value
                    })
                })

                const result = await response.json();

                if (result.success) {
                    window.location.href = 'enterOTP.php';
                } else {
                    email.style.border = '2px solid red';
                    email.focus();
                    emailError.textContent = result.error || 'Failed to send OTP. Please try again.';
                }

            } catch (error) {
                console.error('Error:', error);
            }
        }
    </script>
</body>

</html>