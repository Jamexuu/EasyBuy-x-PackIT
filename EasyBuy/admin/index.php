<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EasyBuy - Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="container-fluid m-0 p-0">
        <div class="p-5" style="background: var(--gradient-color-adminNav);">
        </div>
    </div>
    <div class="container">
        <div class="row mb-3">
            <div class="col text-center">
                <img src="../assets/Group 21987.svg" alt="easybuy logo" class="img-fluid" style="height: 350px;">
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <form action="">
                    <label for="adminEmail" class="form-label">Email</label>
                    <input type="text" class="form-control rounded-3 mb-3" placeholder="Email" name="adminEmail" id="adminEmail">
                    <label for="adminPassword" class="form-label">Password</label>
                    <input type="password" class="form-control rounded-3" placeholder="Password" name="adminPassword" id="adminPassword">

                    <button type="submit" class="btn mt-3 text-white w-100" style="background-color: #6EC064;">Login</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>