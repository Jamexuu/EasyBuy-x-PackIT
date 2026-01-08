<?php

declare(strict_types=1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . "/booking_state.php";

$sessionKeys = ['user_id', 'user', 'logged_in'];
$logged = false;
foreach ($sessionKeys as $k) {
  if (!empty($_SESSION[$k])) {
    $logged = true;
    break;
  }
}

if (!$logged) {
  // Save intended destination for post-login redirect
  $_SESSION['post_login_redirect'] = $_SERVER['REQUEST_URI'] ?? '/';
  header("Location: ../login.php");
  exit;
}

$packages = get_packages();
$error = "";

// Handle submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $selected = $_POST["package"] ?? "";
  $selected = is_string($selected) ? trim($selected) : "";

  $userDesc = trim((string)($_POST["package_desc"] ?? ""));
  $qty = (int)($_POST["package_quantity"] ?? 0);

  if (!$selected || !get_package($selected)) {
    $error = "Please select a valid option.";
  } elseif ($userDesc === "") {
    $error = "Please enter your package description.";
  } elseif ($qty < 1) {
    $error = "Quantity must be at least 1.";
  } else {
    set_selected_package($selected);

    $_SESSION["booking"] ??= [];
    $_SESSION["booking"]["package_desc"] = $userDesc;
    $_SESSION["booking"]["package_quantity"] = $qty;

    header("Location: address.php");
    exit;
  }
}

$state = get_booking_state();
$selectedKey = (string)($state["package_key"] ?? "");

function meters3($l, $w, $h): string
{
  $fmt = fn($x) => rtrim(rtrim(number_format((float)$x, 1), '0'), '.');
  return $fmt($l) . " x " . $fmt($w) . " x " . $fmt($h) . " Meter";
}

// Prepare a JS-friendly map of packages keyed by package key
$packages_js = [];
foreach ($packages as $k => $p) {
  $packages_js[(string)$k] = [
    'vehicle_label' => $p['vehicle_label'] ?? ($p['label'] ?? ''),
    'amount' => isset($p['amount']) ? (float)$p['amount'] : 0
  ];
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PackIT - Choose Package</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root {
      --brand-yellow: #f8e14b;
      --brand-yellow-dark: #e6cc32;
      --brand-black: #1c1c1c;
    }
    body { background-color: #f8f9fa; }
    .card-header { background-color: var(--brand-yellow) !important; color: var(--brand-black) !important; }
    .muted-sm { font-size: .9rem; color: #6c757d; }
    .detail-box { background: #f8f9fa; border-radius: .75rem; padding: .85rem; }
    .pkg-card { border: 1px solid rgba(0, 0, 0, .08); border-radius: .75rem; padding: 1.1rem; background: #fff; cursor: pointer; height: 100%; display:block; }
    .pkg-card.active { box-shadow: 0 0 0 3px rgba(248, 225, 75, .8); border-color: var(--brand-yellow-dark); }
    .price-tag { font-size: 2rem; font-weight: 800; color: var(--brand-black); }
    /* ensure the radio input doesn't visually break the layout */
    .pkg-card .form-check-input { margin-top: .25rem; margin-right: .6rem; }
  </style>
</head>

<body>

  <?php include("../components/navbar.php"); ?>
  <div class="container-fluid py-5 px-3 px-lg-5">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-header p-4 rounded-top-4">
            <h4 class="mb-0 fw-bold"><i class="bi bi-truck"></i> Step 1: Choose Vehicle</h4>
            <p class="mb-0 small opacity-75">Based on your vehicle selection (DB-driven)</p>
          </div>

          <div class="card-body p-4">

            <?php if ($error): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" id="packageForm">
              <div class="row g-3">
                <?php foreach ($packages as $key => $pkg): ?>
                  <?php $sizeText = meters3($pkg["size_length_m"], $pkg["size_width_m"], $pkg["size_height_m"]); ?>
                  <div class="col-lg-4 col-md-6">
                    <label class="pkg-card <?= ($selectedKey === (string)$key) ? "active" : "" ?>">
                      <div class="d-flex align-items-start gap-2">
                        <input class="form-check-input mt-1" type="radio" name="package"
                          value="<?= htmlspecialchars((string)$key) ?>"
                          <?= ($selectedKey === (string)$key) ? "checked" : "" ?>>
                        <div class="w-100">
                          <div class="fw-bold fs-6 mb-2"><?= htmlspecialchars($pkg["label"]) ?></div>

                          <div class="detail-box">
                            <div class="small" style="line-height:1.6;">
                              <small class="text-muted">Examples:</small>
                              <strong><?= htmlspecialchars($pkg["package_type"]) ?></strong><br>

                              <small class="text-muted">Price:</small>
                              <strong>₱<?= number_format((float)$pkg["amount"], 0) ?></strong><br>

                              <small class="text-muted">Max:</small>
                              <strong><?= htmlspecialchars((string)$pkg["max_kg"]) ?> kg</strong><br>

                              <small class="text-muted">Size:</small>
                              <strong><?= htmlspecialchars($sizeText) ?></strong>
                            </div>
                          </div>
                        </div>
                      </div>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>

              <hr class="my-4">

              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label fw-bold">Package Description <span class="text-danger">*</span></label>
                  <textarea class="form-control" name="package_desc" rows="3" required
                    placeholder="Describe your package..."><?= htmlspecialchars((string)($state["package_desc"] ?? "")) ?></textarea>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-bold">Quantity <span class="text-danger">*</span></label>
                  <input class="form-control" type="number" name="package_quantity" min="1" step="1" required
                    value="<?= (int)($state["package_quantity"] ?? 1) ?>">
                </div>

                <div class="col-md-8">
                  <label class="form-label fw-bold">Allowed Specs (Auto)</label>
                  <input class="form-control" type="text" readonly
                    value="<?= htmlspecialchars((string)($state["package_specs_desc"] ?? "")) ?>">
                </div>
              </div>

              <hr class="my-4">

              <div class="row g-3 align-items-center">
                <div class="col-md-7">
                  <div class="muted-sm">Selected result</div>
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
                  <button class="btn btn-warning w-100" type="submit">Save & Continue</button>
                  <div class="muted-sm mt-2">Next: Address & region-based distance fare</div>
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
  <?php include("../components/chat.php"); ?>
  <?php include("../components/footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Map keyed by package key so lookup works by the radio value
    const packages = <?= json_encode($packages_js, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;

    // Update UI fields for a given package key
    function updateDisplayForKey(key) {
      const p = packages[key];
      if (!p) return;
      const vehicleLabelEl = document.getElementById('vehicleLabel');
      const baseAmountEl = document.getElementById('baseAmount');
      const bigPriceEl = document.getElementById('bigPrice');
      if (vehicleLabelEl) vehicleLabelEl.innerText = p.vehicle_label;
      if (baseAmountEl) baseAmountEl.innerText = Number(p.amount).toFixed(0);
      if (bigPriceEl) bigPriceEl.innerText = Number(p.amount).toFixed(2);
    }

    // Remove .active from all cards and add it to the label wrapping the checked radio
    function syncActive() {
      document.querySelectorAll('.pkg-card').forEach(c => c.classList.remove('active'));
      const checked = document.querySelector('input[name="package"]:checked');
      if (checked) {
        const lab = checked.closest('.pkg-card');
        if (lab) lab.classList.add('active');
        updateDisplayForKey(checked.value);
      }
    }

    // Setup listeners
    document.addEventListener('DOMContentLoaded', () => {
      // initial sync (cover server-rendered checked state)
      syncActive();

      // When a radio changes, make sure only its label has .active and update display
      document.querySelectorAll('input[name="package"]').forEach(r => {
        r.addEventListener('change', () => {
          syncActive();
        });

        // optional: support clicking anywhere on the label (label click will already toggle radio,
        // but this ensures immediate visual feedback even if some browsers delay change)
        const label = r.closest('.pkg-card');
        if (label) {
          label.addEventListener('click', (ev) => {
            // clicking the label will toggle the radio; do a small timeout to allow radio.checked to update
            setTimeout(syncActive, 0);
          });
        }
      });
    });
  </script>
</body>
</html>