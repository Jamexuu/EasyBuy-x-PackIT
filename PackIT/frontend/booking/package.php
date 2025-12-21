<?php
declare(strict_types=1);
require_once __DIR__ . "/booking_state.php";

$packages = get_packages();
$error = "";

// Handle submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $selected = $_POST["package"] ?? "";
  $selected = is_string($selected) ? trim($selected) : "";

  if (!$selected || !get_package($selected)) {
    $error = "Please select a valid package.";
  } else {
    set_selected_package($selected);

    // FIX: Redirect to the next step instead of redirecting back to package.php
    header("Location: address.php");
    exit;
  }
}

$state = get_booking_state();
$selectedKey = $state["package_key"] ?? "";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PackIT - Choose Package</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root{
      --brand-yellow:#f8e14b;
      --brand-yellow-dark:#e6cc32;
      --brand-black:#1c1c1c;
    }
    body { background-color: #f8f9fa; }
    .card-header { background-color: var(--brand-yellow) !important; color: var(--brand-black) !important; }
    .btn-primary { background-color: var(--brand-yellow); border-color: var(--brand-yellow); color: var(--brand-black); font-weight: 600; }
    .btn-primary:hover, .btn-primary:focus { background-color: var(--brand-yellow-dark); border-color: var(--brand-yellow-dark); color: var(--brand-black); }
    .pkg-card { border:1px solid rgba(0,0,0,.08); border-radius:.75rem; padding: .9rem; background:#fff; cursor:pointer; height:100%; }
    .pkg-card.active { box-shadow: 0 0 0 3px rgba(248,225,75,.8); border-color: var(--brand-yellow-dark); }
    .price-tag { font-size: 2rem; font-weight: 800; color: var(--brand-black); }
    .muted-sm { font-size:.9rem; color:#6c757d; }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header p-4 rounded-top-4">
          <h4 class="mb-0 fw-bold"><i class="bi bi-box-seam"></i> Step 1: Choose Package</h4>
          <p class="mb-0 small opacity-75">Package determines base price and suggested vehicle</p>
        </div>

        <div class="card-body p-4">

          <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="post" id="packageForm">
            <div class="row g-3">
              <?php foreach ($packages as $key => $pkg): ?>
                <div class="col-md-6">
                  <label class="pkg-card <?= ($selectedKey === $key) ? "active" : "" ?>">
                    <input
                      class="form-check-input me-2"
                      type="radio"
                      name="package"
                      value="<?= htmlspecialchars($key) ?>"
                      <?= ($selectedKey === $key) ? "checked" : "" ?>
                      style="transform: translateY(2px);"
                    >
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <div class="fw-bold"><?= htmlspecialchars($pkg["label"]) ?></div>
                        <div class="muted-sm">Vehicle: <?= htmlspecialchars($pkg["vehicle_label"]) ?></div>
                      </div>
                      <div class="fw-bold">₱<?= number_format((float)$pkg["amount"], 0) ?></div>
                    </div>
                  </label>
                </div>
              <?php endforeach; ?>
            </div>

            <hr class="my-4">

            <div class="row g-3 align-items-center">
              <div class="col-md-7">
                <div class="muted-sm">Selected package result</div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                  <span class="badge text-bg-dark">
                    Vehicle: <span id="vehicleLabel"><?= htmlspecialchars($state["vehicle_label"] ?? "--") ?></span>
                  </span>
                  <span class="badge text-bg-warning">
                    Base: ₱<span id="baseAmount"><?= htmlspecialchars((string)($state["base_amount"] ?? "0")) ?></span>
                  </span>
                </div>
              </div>
              <div class="col-md-5 text-md-end">
                <button class="btn btn-primary w-100" type="submit">
                  Save Package & Continue
                </button>
                <div class="muted-sm mt-2">
                  Next: Address & region-based distance fare
                </div>
              </div>
            </div>
          </form>

          <hr class="my-4">

          <div class="text-center">
            <div class="muted-sm text-uppercase">Current Base Amount</div>
            <div class="price-tag">₱<span id="bigPrice"><?= number_format((float)($state["base_amount"] ?? 0), 2) ?></span></div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Update the “Selected package result” preview without submitting
  const packages = <?=
    json_encode(array_map(fn($p) => [
      "label" => $p["label"],
      "vehicle_label" => $p["vehicle_label"],
      "amount" => $p["amount"],
    ], $packages), JSON_UNESCAPED_SLASHES)
  ?>;

  document.querySelectorAll('input[name="package"]').forEach(r => {
    r.addEventListener('change', () => {
      const key = r.value;
      const p = packages[key];
      if (!p) return;
      document.getElementById('vehicleLabel').innerText = p.vehicle_label;
      document.getElementById('baseAmount').innerText = p.amount;
      document.getElementById('bigPrice').innerText = Number(p.amount).toFixed(2);
    });
  });
</script>
</body>
</html>