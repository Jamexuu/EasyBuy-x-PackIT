<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Transaction Receipt</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root {
      --brand-yellow: #facc15;
      --brand-dark: #1a1a1a;
      --soft-gray: #f3f4f6;
    }

    body {
      background-color: var(--soft-gray);
      font-family: 'Inter', sans-serif;
    }

    .receipt-container {
      max-width: 900px;
      margin: 60px auto;
      background: #fff;
      border-radius: 18px;
      overflow: hidden;
    }

    /* Header */
    .receipt-header {
      padding: 28px;
      background: linear-gradient(90deg, var(--brand-yellow), #facc15);
    }

    .receipt-header h2 {
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    /* Section cards */
    .section-card {
      background: #fff;
      border-radius: 14px;
      padding: 20px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.04);
      margin-bottom: 20px;
    }

    .section-title {
      font-weight: 600;
      font-size: 0.95rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #6b7280;
      margin-bottom: 12px;
    }

    /* Item box */
    .item-list-box {
      background-color: #fffbeb;
      border-radius: 14px;
      padding: 18px;
    }

    /* Payment summary */
    .summary-row {
      display: flex;
      justify-content: space-between;
      padding: 6px 0;
      font-size: 0.95rem;
    }

    .summary-row.total {
      font-size: 1.1rem;
      font-weight: 700;
      border-top: 1px solid #e5e7eb;
      padding-top: 10px;
      margin-top: 8px;
    }

    /* Button */
    .btn-history {
      background-color: var(--brand-yellow);
      border: none;
      font-weight: 600;
      padding: 12px 30px;
      border-radius: 10px;
      transition: 0.3s ease;
    }

    .btn-history:hover {
      background-color: #facc15;
      transform: translateY(-2px);
    }

    /* Table */
    .history-section {
      background: #fafafa;
      padding: 28px;
    }

    table {
      border-radius: 12px;
      overflow: hidden;
    }

    .text-muted-small {
      font-size: 0.85rem;
      color: #6b7280;
    }
  </style>
</head>

<body>

<div class="container">
  <div class="receipt-container shadow">

    <!-- Header -->
    <div class="receipt-header d-flex justify-content-between align-items-center">
      <h2 class="m-0">Transaction Receipt</h2>
      <div class="text-end">
        <div class="fw-semibold">Status</div>
        <span class="badge bg-dark">To Receive</span>
      </div>
    </div>

    <!-- Main Content -->
    <div class="p-4">

      <div class="row g-4">
        <!-- Left -->
        <div class="col-md-7">

          <div class="section-card">
            <div class="section-title">Order Details</div>
            <p class="mb-1"><strong>Order ID:</strong> #12345678</p>
            <p class="mb-1"><strong>Date:</strong> 25.10.2023</p>
            <p class="mb-1"><strong>Name:</strong> Miel</p>
            <p class="mb-1"><strong>Pick-up:</strong> Maganda Heights</p>
            <p class="mb-1"><strong>Drop-off:</strong> Basta</p>
            <p class="mb-0"><strong>Vehicle:</strong> Honda PCX</p>
          </div>

          <div class="section-card">
            <div class="section-title">Payment Summary</div>

            <div class="summary-row">
              <span>Subtotal</span>
              <span>PHP 839</span>
            </div>
            <div class="summary-row">
              <span>Delivery Fee</span>
              <span>PHP 50</span>
            </div>
            <div class="summary-row text-danger">
              <span>Discount</span>
              <span>-PHP 100</span>
            </div>

            <div class="summary-row total">
              <span>Total</span>
              <span>PHP 789</span>
            </div>

            <p class="text-muted-small mt-2">
              Payment Method: Credit Card (**** 1234)
            </p>
          </div>

        </div>

        <!-- Right -->
        <div class="col-md-5">
          <div class="item-list-box h-100">
            <div class="section-title">Items Ordered</div>

            <div class="d-flex justify-content-between mb-2">
              <span>Butterscotch Cake x1</span>
              <span>PHP 367</span>
            </div>

            <div class="d-flex justify-content-between mb-2">
              <span>Chocchip Truffle Cake</span>
              <span>PHP 292</span>
            </div>

            <div class="d-flex justify-content-between">
              <span>Coffee Latte x2</span>
              <span>PHP 880</span>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center mt-4">
        <button class="btn btn-history shadow-sm">
          View Transaction History
        </button>
      </div>

    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
