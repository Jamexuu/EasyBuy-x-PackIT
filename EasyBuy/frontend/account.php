<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Account</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

  <?php include 'components/navbar.php'; ?>

  <div class="container">
    <div class="row m-3">
      <div class="col">
        <div class="h1 my-3">
          Account
        </div>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-11 col-md-10 col-lg-8">
        <div class="card mb-5 rounded-4">
          <div class="card-body">
            <div class="card-title">
              <div class="h3 mb-3">Profile</div>
            </div>
            <div class="card-text">
              <p>Last Name : </p>
              <p>First Name: </p>
              <p>Email: </p>
              <p>Contact Number: </p>
            </div>
          </div>
        </div>
        <div class="card mb-5  rounded-4">
          <div class="card-body">
            <div class="card-title">
              <div class="h3 mb-3">Address</div>
            </div>
            <div class="card-text">
              <p>House No : </p>
              <p>Barangay: </p>
              <p>Province: </p>
              <p>Street: </p>
              <p>City: </p>
              <p>Postal Code: </p>
            </div>
          </div>
        </div>
        <div class="card mb-5 rounded-4">
          <div class="card-body">
            <div class="card-title">
              <div class="h3 mb-3">Payment Details</div>
            </div>
            <div class="card-text">
              <p>Card Owner: </p>
              <p>Card Number: </p>
              <p>Expiration Date: </p>
              <p>Contact Number: </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include 'components/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>


</html>