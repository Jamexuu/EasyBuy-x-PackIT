<?php
declare(strict_types=1);

require_once __DIR__ . "/booking_state.php";
require_once __DIR__ . "/region_map.php";
require_once __DIR__ . "/fare_rules.php";

$state = get_booking_state();
if (empty($state["package_key"])) {
  header("Location: package.php");
  exit;
}

$error = "";
$pickup = [
  "house" => "",
  "barangay" => "",
  "municipality" => "",
  "province" => "",
];
$drop = [
  "house" => "",
  "barangay" => "",
  "municipality" => "",
  "province" => "",
];

// default door-to-door = yes (true)
$doorToDoor = (bool)($state["door_to_door"] ?? true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $pickup["house"] = trim((string)($_POST["pickup_house"] ?? ""));
  $pickup["barangay"] = trim((string)($_POST["pickup_barangay"] ?? ""));
  $pickup["municipality"] = trim((string)($_POST["pickup_municipality"] ?? ""));
  $pickup["province"] = trim((string)($_POST["pickup_province"] ?? ""));

  $drop["house"] = trim((string)($_POST["drop_house"] ?? ""));
  $drop["barangay"] = trim((string)($_POST["drop_barangay"] ?? ""));
  $drop["municipality"] = trim((string)($_POST["drop_municipality"] ?? ""));
  $drop["province"] = trim((string)($_POST["drop_province"] ?? ""));

  $doorToDoor = ((string)($_POST["door_to_door"] ?? "yes")) === "yes";

  if ($pickup["municipality"] === "" || $drop["municipality"] === "") {
    $error = "Please enter both pickup and drop-off municipality.";
  } else {
    $_SESSION["booking"] ??= [];
    $_SESSION["booking"]["pickup_address"] = $pickup;
    $_SESSION["booking"]["drop_address"] = $drop;

    // CONFLICT-FREE resolver (municipality + province, with NCR province aliases)
    $_SESSION["booking"]["pickup_region"] = resolve_region($pickup["municipality"], $pickup["province"]);
    $_SESSION["booking"]["drop_region"] = resolve_region($drop["municipality"], $drop["province"]);

    $_SESSION["booking"]["door_to_door"] = $doorToDoor;

    header("Location: address.php");
    exit;
  }
}

// Reload saved state (after POST redirect)
$state = get_booking_state();
$pickup = $state["pickup_address"] ?? $pickup;
$drop = $state["drop_address"] ?? $drop;

$pickupRegion = $state["pickup_region"] ?? null;
$dropRegion = $state["drop_region"] ?? null;

$baseAmount = (int)($state["base_amount"] ?? 0);
$doorToDoorAmount = get_door_to_door_amount((bool)($state["door_to_door"] ?? true));
$distanceAmount = compute_distance_fare($pickupRegion, $dropRegion);

$totalAmount = null;
if ($distanceAmount !== null) {
  $totalAmount = compute_total_fare($baseAmount, $distanceAmount, $doorToDoorAmount);
}

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, "UTF-8"); }

function format_addr(array $a): string {
  $parts = array_filter([
    (string)($a["house"] ?? ""),
    (string)($a["barangay"] ?? ""),
    (string)($a["municipality"] ?? ""),
    (string)($a["province"] ?? "")
  ]);
  return implode(", ", $parts);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PackIT - Address</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{ --brand-yellow:#f8e14b; --brand-yellow-dark:#e6cc32; --brand-black:#1c1c1c; }
    body { background:#f8f9fa; }
    .card-header { background: var(--brand-yellow) !important; color: var(--brand-black) !important; }
    .btn-primary { background-color: var(--brand-yellow); border-color: var(--brand-yellow); color: var(--brand-black); font-weight:600; }
    .btn-primary:hover { background-color: var(--brand-yellow-dark); border-color: var(--brand-yellow-dark); }
    .muted-sm { font-size:.9rem; color:#6c757d; }
    .price-tag { font-size: 2rem; font-weight: 800; color: var(--brand-black); }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-9 col-md-11">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header p-4 rounded-top-4">
          <h4 class="mb-0 fw-bold">Step 2: Pickup & Drop-off Address</h4>
          <div class="muted-sm">
            Package: <strong><?= h((string)($state["package_label"] ?? "--")) ?></strong>
            · Vehicle: <strong><?= h((string)($state["vehicle_label"] ?? "--")) ?></strong>
          </div>
        </div>

        <div class="card-body p-4">
          <?php if ($error): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
          <?php endif; ?>

          <div class="alert alert-info">
            <div class="fw-bold mb-1">Default pickup address</div>
            <div class="muted-sm mb-2">Frontend-only: loaded from LocalStorage (simulating user registered address).</div>
            <button class="btn btn-sm btn-outline-primary" type="button" id="useDefaultPickupBtn">
              Use default pickup address
            </button>
          </div>

          <form method="post">
            <div class="row g-3">
              <div class="col-md-6">
                <div class="bg-light p-3 rounded">
                  <h6 class="fw-bold">Pickup</h6>

                  <label class="form-label small text-muted">House Address</label>
                  <input class="form-control" name="pickup_house" id="pickup_house" value="<?= h((string)($pickup["house"] ?? "")) ?>">

                  <label class="form-label small text-muted mt-2">Barangay</label>
                  <input class="form-control" name="pickup_barangay" id="pickup_barangay" value="<?= h((string)($pickup["barangay"] ?? "")) ?>">

                  <label class="form-label small text-muted mt-2">Municipality</label>
                  <input class="form-control" name="pickup_municipality" id="pickup_municipality" required value="<?= h((string)($pickup["municipality"] ?? "")) ?>">

                  <label class="form-label small text-muted mt-2">Province</label>
                  <input class="form-control" name="pickup_province" id="pickup_province" value="<?= h((string)($pickup["province"] ?? "")) ?>">

                  <div class="muted-sm mt-2">
                    Region: <strong><?= h((string)($pickupRegion ?? "Unknown")) ?></strong>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="bg-light p-3 rounded">
                  <h6 class="fw-bold">Drop-off</h6>

                  <label class="form-label small text-muted">House Address</label>
                  <input class="form-control" name="drop_house" value="<?= h((string)($drop["house"] ?? "")) ?>">

                  <label class="form-label small text-muted mt-2">Barangay</label>
                  <input class="form-control" name="drop_barangay" value="<?= h((string)($drop["barangay"] ?? "")) ?>">

                  <label class="form-label small text-muted mt-2">Municipality</label>
                  <input class="form-control" name="drop_municipality" required value="<?= h((string)($drop["municipality"] ?? "")) ?>">

                  <label class="form-label small text-muted mt-2">Province</label>
                  <input class="form-control" name="drop_province" value="<?= h((string)($drop["province"] ?? "")) ?>">

                  <div class="muted-sm mt-2">
                    Region: <strong><?= h((string)($dropRegion ?? "Unknown")) ?></strong>
                  </div>
                </div>
              </div>
            </div>

            <hr class="my-4">

            <div class="row g-3 align-items-center">
              <div class="col-md-6">
                <label class="form-label fw-bold">Door-to-door?</label>
                <select class="form-select" name="door_to_door">
                  <option value="yes" <?= ($state["door_to_door"] ?? true) ? "selected" : "" ?>>Yes (+₱100)</option>
                  <option value="no" <?= ($state["door_to_door"] ?? true) ? "" : "selected" ?>>No (+₱0)</option>
                </select>
                <div class="muted-sm mt-2">Door-to-door fee is added to the total.</div>
              </div>

              <div class="col-md-6 text-md-end">
                <button class="btn btn-primary w-100" type="submit">Save Address & Update Fare</button>
              </div>
            </div>
          </form>

          <hr class="my-4">

          <div class="row g-3">
            <div class="col-md-7">
              <div class="small text-muted">Fare breakdown</div>
              <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between">
                  <span>Package base</span>
                  <strong>₱<?= number_format($baseAmount, 0) ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                  <span>Distance fare (by region)</span>
                  <strong>
                    <?php if ($distanceAmount === null): ?>
                      --
                    <?php else: ?>
                      ₱<?= number_format($distanceAmount, 0) ?>
                    <?php endif; ?>
                  </strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                  <span>Door-to-door</span>
                  <strong>₱<?= number_format($doorToDoorAmount, 0) ?></strong>
                </li>
              </ul>

              <?php if ($pickupRegion === null || $dropRegion === null): ?>
                <div class="alert alert-warning mt-3 mb-0">
                  Region mapping is missing for your municipality/province. Add it in <code>region_map.php</code>.
                </div>
              <?php endif; ?>

              <?php if ($pickupRegion === "MINDANAO" && $dropRegion === "MINDANAO"): ?>
                <div class="alert alert-secondary mt-3 mb-0">
                  Special rule applied: Mindanao → Mindanao uses NCR distance amount (₱100).
                </div>
              <?php endif; ?>
            </div>

            <div class="col-md-5 text-center">
              <div class="small text-muted text-uppercase">Total</div>
              <div class="price-tag">
                <?php if ($totalAmount === null): ?>
                  ₱--
                <?php else: ?>
                  ₱<?= number_format($totalAmount, 2) ?>
                <?php endif; ?>
              </div>

              <div class="d-flex gap-2 mt-3">
                <a class="btn btn-outline-secondary w-50" href="package.php">Back</a>
                <a class="btn btn-success w-50 <?= ($totalAmount === null ? "disabled" : "") ?>" href="review.php">
                  Next
                </a>
              </div>
              <div class="muted-sm mt-2">Next page (review.php) shows full summary.</div>
            </div>
          </div>

          <hr class="my-4">
          <div class="small text-muted">
            Preview saved:
            <div><strong>Pickup:</strong> <?= h(format_addr($pickup)) ?></div>
            <div><strong>Drop:</strong> <?= h(format_addr($drop)) ?></div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Simulated "registered address" stored in LocalStorage:
  // key: packit_profile_address
  // { house, barangay, municipality, province }
  function getProfileAddress() {
    try { return JSON.parse(localStorage.getItem("packit_profile_address") || "null"); }
    catch (e) { return null; }
  }

  document.getElementById("useDefaultPickupBtn").addEventListener("click", () => {
    const p = getProfileAddress();
    if (!p) {
      alert("No default profile address found. Open profile_seed.php first to create one.");
      return;
    }
    document.getElementById("pickup_house").value = p.house || "";
    document.getElementById("pickup_barangay").value = p.barangay || "";
    document.getElementById("pickup_municipality").value = p.municipality || "";
    document.getElementById("pickup_province").value = p.province || "";
  });
</script>
</body>
</html>