<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pack IT</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100" style="background-color: #fffef5;">

  <?php include("frontend/components/navbar.php"); ?>

  <main class="flex-grow-1 container py-5">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <h1 class="display-4 fw-bold mb-3">📦 Pack IT</h1>
        <p class="lead mb-4">
          We handle your packaging and delivery so your parcels arrive safe, fast, and hassle-free.
        </p>
        <button class="btn btn-dark btn-lg rounded-pill px-5">Get Started</button>
      </div>
      <div class="col-lg-6 text-center">
      </div>
    </div>
  </main>

  <?php include("frontend/components/footer.php"); ?>

  <?php include("frontend/components/chat.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>