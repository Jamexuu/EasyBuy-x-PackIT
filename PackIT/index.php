<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pack IT</title>


  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


  <style>
    :root {
      --brand-yellow: #f8e15b;
      --brand-dark: #111;
      --brand-gray: #555;
    }


    body {
      font-family: 'Segoe UI', sans-serif;
      overflow-x: hidden;
      background: #fff;
      color: var(--brand-dark);
    }


    .bg-brand {
      background-color: var(--brand-yellow) !important;
    }


    .hover-scale {
      transition: transform 0.2s ease-in-out;
    }

    .hover-scale:hover {
      transform: scale(1.15);
    }


    .footer-curve {
      height: 90px;
      background: var(--brand-yellow);
      clip-path: ellipse(85% 100% at 50% 100%);
    }


    .floating-actions {
      position: fixed;
      top: 50%;
      right: 20px;
      transform: translateY(-50%);
      background: #f8e15b;
      border-radius: 10px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      gap: 25px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
      z-index: 1050;
      width: 120px;
    }


    .floating-actions button {
      width: 100%;
      border-radius: 5px;
    }


    .floating-actions a {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-decoration: none;
      color: var(--brand-dark);
      font-size: 0.9rem;
      transition: transform 0.2s;
    }


    .floating-actions a img {
      width: 45px;
      height: 45px;
      margin-bottom: 5px;
    }


    .floating-actions a:hover {
      transform: scale(1.1);
    }


    @media (max-width: 991px) {
      h1.display-1 {
        font-size: 3rem;
      }

      .floating-actions {
        top: auto;
        bottom: 80px;
        right: 20px;
        flex-direction: row;
        border-radius: 12px;
        padding: 10px 15px;
        gap: 15px;
      }

      .floating-actions a img {
        width: 35px;
        height: 35px;
      }
    }
  </style>
</head>


<body id="top" class="min-vh-100">


  <?php $page = basename($_SERVER['PHP_SELF']); ?>
  <?php include("frontend/components/navbar.php"); ?>


  <main class="container my-5 py-lg-5">
    <div class="row align-items-center gy-5">
      <div class="col-lg-6 text-center text-lg-start">
        <h1 class="display-1 fw-black text-uppercase">PACK IT</h1>
        <p class="lead mt-4" style="color: var(--brand-gray);">
          The gold standard of PH logistics. 🏆<br>
          Bridging gaps and breaking records, one delivery at a time.
        </p>
        <a href="..." class="btn bg-brand btn-lg fw-bold rounded-pill px-4 mt-3 hover-scale">Get Started</a>
      </div>


      <div class="col-lg-6 text-center">
        <img src="assets/mascot.png" class="img-fluid" alt="Mascot" style="max-height: 450px;">
      </div>
    </div>
  </main>


  <div class="floating-actions">
    <a href="../PackIT/frontend/booking/package.php">
      <img src="assets/box.png" alt="book">
      <span>Book</span>
    </a>
    <a href="../PackIT/frontend/tracking.php">
      <img src="assets/tracking.png" alt="tracking">
      <span>Tracking</span>
    </a>
    <a href="../PackIT/frontend/chatai.php">
      <img src="assets/chatbot.png" alt="Chatbot">
      <span>Chatbot</span>
    </a>
  </div>




  <?php include("frontend/components/footer.php"); ?>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>