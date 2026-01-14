<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FAQ - Easy Buy</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=shopping_cart"
    rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
    .accordion-button:not(.collapsed) {
      font-weight: bold;
      color: #6EC064;
    }
  </style>
</head>

<body>
  <?php include './components/navbar.php'; ?>

  <div class="container fw-normal mt-5">
    <h2 class="fw-bold" style="color: #6EC064;">Frequently Asked Questions (FAQ)</h2>
    <p class="text-muted">Welcome to the EasyBuy FAQ! We're an online grocery store making it simple to shop for fresh produce and pantry staples from the comfort of home. Find answers to common questions below.
      If you don't see what you need, contact our support team.</p>
  </div>

  <div class="container mt-5 mb-5">
    <div class="accordion accordion-flush" id="accordionFlushExample">
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
            Account and Registration
          </button>
        </h2>
        <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
          <div class="accordion-body">
            <div class="fw-bold mb-0">How do I create an account?</div>
            <div class="fw-normal">Visit easybuy.com, click "Sign Up" in the top right, and fill in your email, password, and delivery details. It takes under 2 minutes, and you can start shopping right away.</div>
            <div class="fw-bold mb-0 mt-3">What if I forget my password?</div>
            <div class="fw-normal">On the login page, click "Forgot Password?" Enter your email, and we'll send a reset link. Check your spam folder if it doesn't arrive within 5 minutes.</div>
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
            Orders and Delivery
          </button>
        </h2>
        <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
          <div class="accordion-body">
            <div class="fw-bold mb-0">What is the shipping fee?</div>
            <div class="fw-normal">The standard shipping fee is a fixed ₱100. Free on orders over ₱1,000; no minimum order required.</div>
            <div class="fw-bold mb-0 mt-3">Can I edit or cancel my order?</div>
            <div class="fw-normal">Edit or cancel anytime can be done via "My Orders".</div>
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
            Payments
          </button>
        </h2>
        <div id="flush-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
          <div class="accordion-body">
            <div class="fw-bold mb-0">What payment methods are accepted?</div>
            <div class="fw-normal">PayPal and Bank transfer for online or e-wallet payment and cash on delivery (COD) is offered.</div>
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
            Shopping and Products
          </button>
        </h2>
        <div id="flush-collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
          <div class="accordion-body">
            <div class="fw-bold mb-0">What products do you offer?</div>
            <div class="fw-normal">We stock fresh fruits and vegetables, dairy, meat, snacks, beverages, and more from local and national brands.</div>
            <div class="fw-bold mb-0 mt-3">Can I see product availability and prices?</div>
            <div class="fw-normal">Yes, browse categories to view product categories, or use the search bar. Prices are listed clearly with its sizes in grams.</div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <?php include './components/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>