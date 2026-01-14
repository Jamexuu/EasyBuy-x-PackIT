<?php

declare(strict_types=1);

require_once __DIR__ . "/booking_state.php";
require_once __DIR__ . "/fare_rules.php";
require_once __DIR__ . "/../../api/paypal/paypal_config.php";

$state = get_booking_state();
if (empty($state["package_key"])) {
  header("Location: package.php");
  exit;
}
if (empty($state["pickup_address"]) || empty($state["drop_address"])) {
  header("Location: address.php");
  exit;
}

// Require contacts before review
if (
  empty($state["pickup_contact_name"]) || empty($state["pickup_contact_number"]) ||
  empty($state["drop_contact_name"]) || empty($state["drop_contact_number"])
) {
  header("Location: address.php");
  exit;
}

$baseAmount = (int)($state["base_amount"] ?? 0);
$pickupRegion = $state["pickup_region"] ?? null;
$dropRegion = $state["drop_region"] ?? null;

$distanceAmount = compute_distance_fare($pickupRegion, $dropRegion);
if ($distanceAmount === null) {
  header("Location: address.php");
  exit;
}

$totalAmount = compute_total_fare($baseAmount, $distanceAmount, 0);

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function fmt1(float $x): string { return rtrim(rtrim(number_format($x, 1), '0'), '.'); }

function format_addr(array $a): string {
  $parts = array_filter([$a["house"] ?? "", $a["barangay"] ?? "", $a["municipality"] ?? "", $a["province"] ?? ""]);
  return implode(", ", $parts);
}

$pickupText = format_addr((array)$state["pickup_address"]);
$dropText = format_addr((array)$state["drop_address"]);

$vehicleLabel = (string)($state["vehicle_label"] ?? '');
$packageType  = (string)($state["package_type"] ?? '');
$maxKg        = (int)($state["max_kg"] ?? 0);
$sizeL        = (float)($state["size_length_m"] ?? 0);
$sizeW        = (float)($state["size_width_m"] ?? 0);
$sizeH        = (float)($state["size_height_m"] ?? 0);

$userDesc = (string)($state["package_desc"] ?? '');
$qty = (int)($state["package_quantity"] ?? 1);
$specsDesc = (string)($state["package_specs_desc"] ?? '');

$pickupContactName = (string)$state["pickup_contact_name"];
$pickupContactNumber = (string)$state["pickup_contact_number"];
$dropContactName = (string)$state["drop_contact_name"];
$dropContactNumber = (string)$state["drop_contact_number"];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PackIT - Review</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root { --brand-yellow:#f8e14b; --brand-yellow-dark:#e6cc32; --brand-black:#1c1c1c; }
    body { background:#f8f9fa; }
    .card-header { background:var(--brand-yellow) !important; color:var(--brand-black) !important; }
    .price-tag { font-size:2.2rem; font-weight:900; color:var(--brand-black); }
    .muted-sm { font-size:.9rem; color:#6c757d; }
    .detail-box { background:#f8f9fa; border-radius:.75rem; padding:1rem; }
  </style>
  <script src="https://www.paypal.com/sdk/js?client-id=<?= PAYPAL_CLIENT_ID ?>&currency=PHP"></script>
</head>
<body>
  <?php include("../components/navbar.php"); ?>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-md-10">
        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-header p-4 rounded-top-4">
            <div class="muted-sm">TOTAL</div>
            <div class="price-tag">₱<?= number_format($totalAmount, 0) ?></div>
            <div class="muted-sm">Vehicle: <?= h($vehicleLabel) ?></div>
          </div>

          <div class="card-body p-4">
            <h5 class="mb-3">Step 3: Review Booking</h5>

            <div class="mb-4">
              <h6>Details</h6>
              <div class="detail-box">

                <div class="mb-3">
                  <div class="muted-sm mb-1">Package</div>
                  <div><?= h($vehicleLabel) ?> (Base ₱<?= number_format($baseAmount, 0) ?>)</div>
                  <div class="mt-2 p-3 rounded-3" style="background:#fff;border:1px solid rgba(0,0,0,.08)">
                    <div><strong>Quantity:</strong> <?= (int)$qty ?></div>
                    <div class="mt-2"><strong>Description:</strong><br><?= h($userDesc) ?></div>

                    <div class="mt-3 text-muted small">
                      <strong>Limits (auto):</strong>
                      <?= h($specsDesc !== '' ? $specsDesc : ("Type: $packageType | Max: $maxKg kg | Size: ".fmt1($sizeL)." x ".fmt1($sizeW)." x ".fmt1($sizeH)." Meter")) ?>
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <div class="muted-sm">Pickup</div>
                  <div class="mb-1"><?= h($pickupText) ?></div>
                  <div class="muted-sm">Contact: <strong><?= h($pickupContactName) ?></strong> • <strong><?= h($pickupContactNumber) ?></strong></div>
                  <div class="muted-sm">Region: <strong><?= h((string)($state["pickup_region"] ?? '')) ?></strong></div>
                </div>

                <div class="mb-3">
                  <div class="muted-sm">Drop-off</div>
                  <div class="mb-1"><?= h($dropText) ?></div>
                  <div class="muted-sm">Recipient: <strong><?= h($dropContactName) ?></strong> • <strong><?= h($dropContactNumber) ?></strong></div>
                  <div class="muted-sm">Region: <strong><?= h((string)($state["drop_region"] ?? '')) ?></strong></div>
                </div>

              </div>
            </div>

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
                <span class="fw-bold">Total</span>
                <strong class="fw-bold">₱<?= number_format($totalAmount, 0) ?></strong>
              </li>
            </ul>

            <div class="d-flex gap-2 align-items-center flex-column">
              <div id="paypal-button-container" class="col-12 col-md-10 col-lg-8"></div>
              
              <div class="small text-muted text-center" style="max-width: 520px;">
                You will be redirected to the success page after payment. Please ensure you are logged in before booking.
              </div>

              <a class="btn btn-outline-secondary btn-sm mt-2" href="address.php" style="width: 200px;">Back to Address</a>
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

  <?php include("../components/chat.php"); ?>
  <?php include("../components/footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    paypal.Buttons({
      createOrder: function(data, actions) {
        return fetch("../../api/paypal/paypal_api.php", {
            method: "post",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "create_order" })
          })
          .then(res => res.json())
          .then(orderData => {
            if (orderData && orderData.error) {
              alert(orderData.error);
              throw new Error(orderData.error);
            }
            if (!orderData || !orderData.id) {
              throw new Error("Could not create PayPal order.");
            }
            return orderData.id;
          });
      },

      onApprove: function(data, actions) {
        return fetch("../../api/paypal/paypal_api.php", {
            method: "post",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              action: "capture_order",
              orderID: data.orderID
            })
          })
          .then(res => res.json())
          .then(details => {
            if (details && details.error) {
              alert("Payment/booking failed: " + details.error);
              return;
            }

            const bookingId = details ? (details._packit_booking_id || "") : "";
            if (!bookingId) {
              alert("Payment completed, but booking reference is missing. Please contact support.");
              window.location.href = "../../api/paypal/success.php";
              return;
            }

            window.location.href = "../../api/paypal/success.php?booking_id=" + encodeURIComponent(bookingId);
          });
      },

      onError: function(err) {
        console.error(err);
        alert("An error occurred during payment.");
      }
    }).render('#paypal-button-container');
  </script>
</body>
</html>