<?php
declare(strict_types=1);
require_once __DIR__ . "/booking_state.php";
require_once __DIR__ . "/fare_rules.php";

$state = get_booking_state();
if (empty($state["package_key"])) { header("Location: package.php"); exit; }
if (empty($state["pickup_address"]) || empty($state["drop_address"])) { header("Location: address.php"); exit; }

$baseAmount = (int)($state["base_amount"] ?? 0);
$pickupRegion = $state["pickup_region"] ?? null;
$dropRegion = $state["drop_region"] ?? null;

$distanceAmount = compute_distance_fare($pickupRegion, $dropRegion);
$doorToDoorAmount = get_door_to_door_amount((bool)($state["door_to_door"] ?? true));

if ($distanceAmount === null) {
  header("Location: address.php");
  exit;
}

$totalAmount = compute_total_fare($baseAmount, $distanceAmount, $doorToDoorAmount);

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function format_addr(array $a): string {
  $parts = array_filter([$a["house"] ?? "", $a["barangay"] ?? "", $a["municipality"] ?? "", $a["province"] ?? ""]);
  return implode(", ", $parts);
}

$pickupText = format_addr((array)$state["pickup_address"]);
$dropText = format_addr((array)$state["drop_address"]);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PackIT - Review</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{ --brand-yellow:#f8e14b; --brand-yellow-dark:#e6cc32; --brand-black:#1c1c1c; }
    body { background:#f8f9fa; }
    .card-header { background: var(--brand-yellow) !important; color: var(--brand-black) !important; }
    .btn-primary { background-color: var(--brand-yellow); border-color: var(--brand-yellow); color: var(--brand-black); font-weight:600; }
    .btn-primary:hover { background-color: var(--brand-yellow-dark); border-color: var(--brand-yellow-dark); }
    .price-tag { font-size: 2.2rem; font-weight: 900; color: var(--brand-black); }
    .muted-sm { font-size:.9rem; color:#6c757d; }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header p-4 rounded-top-4">
          <h4 class="mb-0 fw-bold">Step 3: Review Booking</h4>
          <div class="muted-sm">Confirm details before payment (payment step next)</div>
        </div>

        <div class="card-body p-4">
          <div class="text-center mb-3">
            <div class="small text-muted text-uppercase">Total</div>
            <div class="price-tag">₱<?= number_format($totalAmount, 2) ?></div>
            <div class="muted-sm">Vehicle: <strong><?= h((string)($state["vehicle_label"] ?? "--")) ?></strong></div>
          </div>

          <hr>

          <h6 class="fw-bold">Details</h6>
          <ul class="list-group mb-3">
            <li class="list-group-item">
              <div class="fw-bold">Package</div>
              <div class="muted-sm"><?= h((string)($state["package_label"] ?? "--")) ?> (Base ₱<?= number_format($baseAmount, 0) ?>)</div>
            </li>
            <li class="list-group-item">
              <div class="fw-bold">Pickup</div>
              <div class="muted-sm"><?= h($pickupText) ?></div>
              <div class="muted-sm">Region: <strong><?= h((string)$pickupRegion) ?></strong></div>
            </li>
            <li class="list-group-item">
              <div class="fw-bold">Drop-off</div>
              <div class="muted-sm"><?= h($dropText) ?></div>
              <div class="muted-sm">Region: <strong><?= h((string)$dropRegion) ?></strong></div>
            </li>
          </ul>

          <h6 class="fw-bold">Fare breakdown</h6>
          <ul class="list-group mb-4">
            <li class="list-group-item d-flex justify-content-between">
              <span>Package base</span>
              <strong>₱<?= number_format($baseAmount, 0) ?></strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Distance fare</span>
              <strong>₱<?= number_format($distanceAmount, 0) ?></strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Door-to-door</span>
              <strong>₱<?= number_format($doorToDoorAmount, 0) ?></strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span class="fw-bold">Total</span>
              <strong class="fw-bold">₱<?= number_format($totalAmount, 0) ?></strong>
            </li>
          </ul>

          <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary w-50" href="address.php">Back</a>
            <button class="btn btn-success w-50" type="button" onclick="alert('Next: payment integration (PayPal) like your old step 3/4)')">
              Proceed to Payment
            </button>
          </div>

          <?php if ($pickupRegion === "MINDANAO" && $dropRegion === "MINDANAO"): ?>
            <div class="alert alert-secondary mt-3 mb-0">
              Special rule applied: Mindanao → Mindanao uses NCR distance amount (₱100).
            </div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>