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
    $error = "Please select a valid option.";
  } else {
    set_selected_package($selected);
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
    :root {
      --brand-yellow: #f8e14b;
      --brand-yellow-dark: #e6cc32;
      --brand-black: #1c1c1c;
    }

    body {
      background-color: #f8f9fa;
    }

    .card-header {
      background-color: var(--brand-yellow) !important;
      color: var(--brand-black) !important;
    }

    .btn-primary {
      background-color: var(--brand-yellow);
      border-color: var(--brand-yellow);
      color: var(--brand-black);
      font-weight: 600;
    }

    .btn-primary:hover,
    .btn-primary:focus {
      background-color: var(--brand-yellow-dark);
      border-color: var(--brand-yellow-dark);
      color: var(--brand-black);
    }

    .pkg-card {
      border: 1px solid rgba(0, 0, 0, .08);
      border-radius: .75rem;
      padding: 1.1rem;
      background: #fff;
      cursor: pointer;
      height: 100%;
    }

    .pkg-card.active {
      box-shadow: 0 0 0 3px rgba(248, 225, 75, .8);
      border-color: var(--brand-yellow-dark);
    }

    .muted-sm {
      font-size: .9rem;
      color: #6c757d;
    }

    .detail-box {
      background: #f8f9fa;
      border-radius: .75rem;
      padding: .85rem;
    }

    .price-tag {
      font-size: 2rem;
      font-weight: 800;
      color: var(--brand-black);
    }
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
                        <input
                          class="form-check-input mt-1"
                          type="radio"
                          name="package"
                          value="<?= htmlspecialchars((string)$key) ?>"
                          <?= ($selectedKey === (string)$key) ? "checked" : "" ?>>
                        <div class="w-100">
                          <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="fw-bold fs-6"><?= htmlspecialchars($pkg["label"]) ?></div>
                          </div>

                          <div class="detail-box">
                            <div class="small" style="line-height:1.6;">
                              <small class="text-muted">Type:</small>
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
                  <button class="btn btn-primary w-100" type="submit">
                    Save & Continue
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
  <?php include("../components/footer.php"); ?>
  <script>
    const packages = <?=
                      json_encode(array_map(fn($p) => [
                        "label" => $p["label"],
                        "vehicle_label" => $p["vehicle_label"],
                        "amount" => $p["amount"],
                        "package_type" => $p["package_type"],
                        "max_kg" => $p["max_kg"],
                        "size_length_m" => $p["size_length_m"],
                        "size_width_m" => $p["size_width_m"],
                        "size_height_m" => $p["size_height_m"],
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