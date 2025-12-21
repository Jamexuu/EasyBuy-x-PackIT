<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pack IT</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

  <style>
    /* Chat Button Styles */
    .poc-chat-btn {
      position: fixed;
      bottom: 24px;
      right: 24px;
      width: 64px;
      height: 64px;
      border-radius: 50%;
      background: #facc15;
      color: #1f2937;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      cursor: pointer;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
      transition: transform 0.2s, box-shadow 0.2s;
      z-index: 1000;
    }

    .poc-chat-btn:hover {
      transform: scale(1.08);
      box-shadow: 0 12px 28px rgba(0, 0, 0, 0.3);
    }

    .poc-tooltip {
      position: absolute;
      bottom: 75px;
      right: 0;
      background: #111827;
      color: white;
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 13px;
      white-space: nowrap;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.2s;
    }

    .poc-chat-btn:hover .poc-tooltip {
      opacity: 1;
    }
  </style>
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

  <div class="poc-chat-btn" onclick="goToChat()">
    💬
    <div class="poc-tooltip">Chat with POC</div>
  </div>

  <?php include("frontend/components/footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function goToChat() {
      window.location.href = "frontend/chatai.php";
    }
  </script>

</body>

</html>