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


function h(string $s): string
{
  return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}


function loadJson(string $filename): array
{
  $path = __DIR__ . '/../../assets/json/' . $filename;
  if (!file_exists($path)) return [];
  $json = file_get_contents($path);
  $data = json_decode($json ?: "[]", true);
  return is_array($data) ? $data : [];
}


$error = "";


// Load datasets
$regions = loadJson("region.json");
$provinces = loadJson("province.json");
$cities = loadJson("city.json");
$barangays = loadJson("barangay.json");


// ---------- AJAX HANDLERS ----------
if (isset($_GET["action"])) {
  header("Content-Type: application/json; charset=utf-8");
  $action = (string)$_GET["action"];
  $response = [];


  if ($action === "get_regions") {
    $response = $regions;
  } elseif ($action === "get_provinces") {
    usort($provinces, fn($a, $b) => strcmp($a['province_name'], $b['province_name']));
    $response = $provinces;
  } elseif ($action === "get_cities") {
    $code = (string)($_GET["province_code"] ?? "");
    $response = array_values(array_filter($cities, fn($i) => (string)($i["province_code"] ?? "") === $code));
  } elseif ($action === "get_barangays") {
    $code = (string)($_GET["city_code"] ?? "");
    $response = array_values(array_filter($barangays, fn($i) => (string)($i["city_code"] ?? "") === $code));
 
  // NEW: Calculate Fare via AJAX for live updates
  } elseif ($action === "calculate_fare") {
      $pRegion = (string)($_GET["pickup_region"] ?? "");
      $dRegion = (string)($_GET["drop_region"] ?? "");
      $isDoor = ((string)($_GET["door_to_door"] ?? "yes") === "yes");


      $pickupFareRegion = region_code_to_fare_region($pRegion);
      $dropFareRegion = region_code_to_fare_region($dRegion);


      $base = (int)($state["base_amount"] ?? 0);
      $dist = compute_distance_fare($pickupFareRegion, $dropFareRegion);
      $door = get_door_to_door_amount($isDoor);
     
      $total = 0;
      $valid = false;
     
      if ($dist !== null) {
          $total = compute_total_fare($base, $dist, $door);
          $valid = true;
      }


      $response = [
          "valid" => $valid,
          "base" => number_format($base, 0),
          "distance" => ($dist !== null) ? number_format($dist, 0) : "--",
          "door" => number_format($door, 0),
          "total" => ($valid) ? number_format($total, 2) : "--"
      ];
  }


  echo json_encode($response, JSON_UNESCAPED_UNICODE);
  exit;
}


// ---------- FORM STATE ----------
$pickup = [
  "house" => "",
  "barangay" => "",
  "municipality" => "",
  "province" => "",
  "region_name" => "",
  "region_code" => "",
  "province_code" => "",
  "city_code" => "",
  "brgy_code" => "",
];


$drop = [
  "house" => "",
  "barangay" => "",
  "municipality" => "",
  "province" => "",
  "region_name" => "",
  "region_code" => "",
  "province_code" => "",
  "city_code" => "",
  "brgy_code" => "",
];


$doorToDoor = (bool)($state["door_to_door"] ?? true);


if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Pickup
  $pickup["house"] = trim((string)($_POST["pickup_house"] ?? ""));
  $pickup["region_code"] = (string)($_POST["pickup_region_code"] ?? "");
  $pickup["province_code"] = (string)($_POST["pickup_province_code"] ?? "");
  $pickup["city_code"] = (string)($_POST["pickup_city_code"] ?? "");
  $pickup["brgy_code"] = (string)($_POST["pickup_brgy_code"] ?? "");
  $pickup["province"] = trim((string)($_POST["pickup_province_name"] ?? ""));
  $pickup["municipality"] = trim((string)($_POST["pickup_city_name"] ?? ""));
  $pickup["barangay"] = trim((string)($_POST["pickup_barangay_name"] ?? ""));
  $pickup["region_name"] = trim((string)($_POST["pickup_region_name"] ?? ""));


  // Drop
  $drop["house"] = trim((string)($_POST["drop_house"] ?? ""));
  $drop["region_code"] = (string)($_POST["drop_region_code"] ?? "");
  $drop["province_code"] = (string)($_POST["drop_province_code"] ?? "");
  $drop["city_code"] = (string)($_POST["drop_city_code"] ?? "");
  $drop["brgy_code"] = (string)($_POST["drop_brgy_code"] ?? "");
  $drop["province"] = trim((string)($_POST["drop_province_name"] ?? ""));
  $drop["municipality"] = trim((string)($_POST["drop_city_name"] ?? ""));
  $drop["barangay"] = trim((string)($_POST["drop_barangay_name"] ?? ""));
  $drop["region_name"] = trim((string)($_POST["drop_region_name"] ?? ""));


  $doorToDoor = ((string)($_POST["door_to_door"] ?? "yes")) === "yes";


  if (
    $pickup["province_code"] === "" || $pickup["city_code"] === "" ||
    $drop["province_code"] === "" || $drop["city_code"] === ""
  ) {
    $error = "Please select both pickup and drop-off locations before proceeding.";
  } else {
    $pickupFareRegion = region_code_to_fare_region($pickup["region_code"]);
    $dropFareRegion = region_code_to_fare_region($drop["region_code"]);


    $_SESSION["booking"] ??= [];
    $_SESSION["booking"]["pickup_address"] = $pickup;
    $_SESSION["booking"]["drop_address"] = $drop;
    $_SESSION["booking"]["pickup_region"] = $pickupFareRegion;
    $_SESSION["booking"]["drop_region"] = $dropFareRegion;
    $_SESSION["booking"]["door_to_door"] = $doorToDoor;


    // UPDATED: Redirect directly to review page on submit
    header("Location: review.php");
    exit;
  }
}


// Reload saved state
$state = get_booking_state();
$pickup = array_merge($pickup, (array)($state["pickup_address"] ?? []));
$drop = array_merge($drop, (array)($state["drop_address"] ?? []));


$pickupRegion = $state["pickup_region"] ?? null;
$dropRegion = $state["drop_region"] ?? null;


$baseAmount = (int)($state["base_amount"] ?? 0);
$doorToDoorAmount = get_door_to_door_amount((bool)($state["door_to_door"] ?? true));
$distanceAmount = compute_distance_fare($pickupRegion, $dropRegion);


$totalAmount = null;
if ($distanceAmount !== null) {
  $totalAmount = compute_total_fare($baseAmount, $distanceAmount, $doorToDoorAmount);
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
    :root {
      --brand-yellow: #f8e14b;
      --brand-yellow-dark: #e6cc32;
      --brand-black: #1c1c1c;
    }


    body {
      background: #f8f9fa;
    }


    .card-header {
      background: var(--brand-yellow) !important;
      color: var(--brand-black) !important;
    }


    .btn-primary {
      background-color: var(--brand-yellow);
      border-color: var(--brand-yellow);
      color: var(--brand-black);
      font-weight: 600;
    }


    .btn-primary:hover {
      background-color: var(--brand-yellow-dark);
      border-color: var(--brand-yellow-dark);
    }
   
    .btn-success {
        font-weight: 600;
    }


    .muted-sm {
      font-size: .9rem;
      color: #6c757d;
    }


    .price-tag {
      font-size: 2rem;
      font-weight: 800;
      color: var(--brand-black);
    }
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


          <div class="card-body p-4">
            <?php if ($error): ?>
              <div class="alert alert-danger"><?= h($error) ?></div>
            <?php endif; ?>


            <div class="alert alert-info">
              <div class="fw-bold mb-1">Default pickup address</div>
              <div class="muted-sm mb-2">Frontend-only: loaded from LocalStorage.</div>
              <button class="btn btn-sm btn-outline-primary" type="button" id="useDefaultPickupBtn">
                Use default pickup address
              </button>
            </div>


            <form method="post" id="addressForm">


              <input type="hidden" name="pickup_region_code" id="pickup_region_code" value="<?= h((string)($pickup["region_code"] ?? "")) ?>">
              <input type="hidden" name="pickup_region_name" id="pickup_region_name" value="<?= h((string)($pickup["region_name"] ?? "")) ?>">
              <input type="hidden" name="pickup_province_name" id="pickup_province_name" value="<?= h((string)($pickup["province"] ?? "")) ?>">
              <input type="hidden" name="pickup_city_name" id="pickup_city_name" value="<?= h((string)($pickup["municipality"] ?? "")) ?>">
              <input type="hidden" name="pickup_barangay_name" id="pickup_barangay_name" value="<?= h((string)($pickup["barangay"] ?? "")) ?>">


              <input type="hidden" name="drop_region_code" id="drop_region_code" value="<?= h((string)($drop["region_code"] ?? "")) ?>">
              <input type="hidden" name="drop_region_name" id="drop_region_name" value="<?= h((string)($drop["region_name"] ?? "")) ?>">
              <input type="hidden" name="drop_province_name" id="drop_province_name" value="<?= h((string)($drop["province"] ?? "")) ?>">
              <input type="hidden" name="drop_city_name" id="drop_city_name" value="<?= h((string)($drop["municipality"] ?? "")) ?>">
              <input type="hidden" name="drop_barangay_name" id="drop_barangay_name" value="<?= h((string)($drop["barangay"] ?? "")) ?>">


              <div class="row g-3">
                <div class="col-md-6">
                  <div class="bg-light p-3 rounded h-100">
                    <h6 class="fw-bold text-primary mb-3">Pickup Location</h6>


                    <label class="form-label small text-muted">House Address</label>
                    <input class="form-control" name="pickup_house" id="pickup_house" value="<?= h((string)($pickup["house"] ?? "")) ?>">


                    <label class="form-label small text-muted mt-2">Province</label>
                    <select class="form-select" id="pickup_province" name="pickup_province_code" required>
                      <option value="">Select Province</option>
                    </select>


                    <label class="form-label small text-muted mt-2">City / Municipality</label>
                    <select class="form-select" id="pickup_city" name="pickup_city_code" disabled required>
                      <option value="">Select City</option>
                    </select>


                    <label class="form-label small text-muted mt-2">Barangay</label>
                    <select class="form-select" id="pickup_barangay" name="pickup_brgy_code" disabled>
                      <option value="">Select Barangay (optional)</option>
                    </select>
                  </div>
                </div>


                <div class="col-md-6">
                  <div class="bg-light p-3 rounded h-100">
                    <h6 class="fw-bold text-primary mb-3">Drop-off Location</h6>


                    <label class="form-label small text-muted">House Address</label>
                    <input class="form-control" name="drop_house" id="drop_house" value="<?= h((string)($drop["house"] ?? "")) ?>">


                    <label class="form-label small text-muted mt-2">Province</label>
                    <select class="form-select" id="drop_province" name="drop_province_code" required>
                      <option value="">Select Province</option>
                    </select>


                    <label class="form-label small text-muted mt-2">City / Municipality</label>
                    <select class="form-select" id="drop_city" name="drop_city_code" disabled required>
                      <option value="">Select City</option>
                    </select>


                    <label class="form-label small text-muted mt-2">Barangay</label>
                    <select class="form-select" id="drop_barangay" name="drop_brgy_code" disabled>
                      <option value="">Select Barangay (optional)</option>
                    </select>
                  </div>
                </div>
              </div>


              <hr class="my-4">


              <div class="row g-3 align-items-center">
                <div class="col-md-6">
                  <label class="form-label fw-bold">Door-to-door?</label>
                  <select class="form-select" name="door_to_door" id="door_to_door">
                    <option value="yes" <?= ($state["door_to_door"] ?? true) ? "selected" : "" ?>>Yes (+₱100)</option>
                    <option value="no" <?= ($state["door_to_door"] ?? true) ? "" : "selected" ?>>No (+₱0)</option>
                  </select>
                </div>
               
                </div>
           


            <hr class="my-4">


            <div class="row g-3">
              <div class="col-md-7">
                <div class="small text-muted">Fare breakdown</div>
                <ul class="list-group">
                  <li class="list-group-item d-flex justify-content-between">
                    <span>Package base</span>
                    <strong id="disp_base">₱<?= number_format($baseAmount, 0) ?></strong>
                  </li>
                  <li class="list-group-item d-flex justify-content-between">
                    <span>Distance fare</span>
                    <strong id="disp_distance">
                      <?php if ($distanceAmount === null): ?>
                        --
                      <?php else: ?>
                        ₱<?= number_format($distanceAmount, 0) ?>
                      <?php endif; ?>
                    </strong>
                  </li>
                  <li class="list-group-item d-flex justify-content-between">
                    <span>Door-to-door</span>
                    <strong id="disp_door">₱<?= number_format($doorToDoorAmount, 0) ?></strong>
                  </li>
                </ul>
              </div>


              <div class="col-md-5 text-center">
                <div class="small text-muted text-uppercase">Total</div>
                <div class="price-tag" id="disp_total">
                  <?php if ($totalAmount === null): ?>
                    ₱--
                  <?php else: ?>
                    ₱<?= number_format($totalAmount, 2) ?>
                  <?php endif; ?>
                </div>


                <div class="d-flex gap-2 mt-3">
                  <a class="btn btn-outline-secondary w-50" href="package.php">Back</a>
                  <button type="submit" class="btn btn-success w-50" <?= ($totalAmount === null ? "disabled" : "") ?> id="btnNext">
                    Next
                  </button>
                </div>
              </div>
            </div>
            </form>


          </div>
        </div>
      </div>
    </div>
  </div>


  <script>
    let REGION_MAP = {};


    async function fetchData(params) {
      try {
        const res = await fetch(`?${params}`);
        return await res.json();
      } catch (e) {
        console.error(e);
        return [];
      }
    }


    function populate(selectEl, data, codeKey, nameKey, datasetAttrs = {}) {
      selectEl.innerHTML = '<option value="">Select Option</option>';
      selectEl.disabled = false;
     
      data.sort((a, b) => (a[nameKey] || '').localeCompare((b[nameKey] || '')));


      data.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item[codeKey];
        opt.textContent = item[nameKey];
        opt.dataset.name = item[nameKey] || '';
        for (const [key, field] of Object.entries(datasetAttrs)) {
            opt.dataset[key] = item[field] || '';
        }
        selectEl.appendChild(opt);
      });
    }
    document.getElementById("pickup_house").value = p.house || "";
    document.getElementById("pickup_barangay").value = p.barangay || "";
    document.getElementById("pickup_municipality").value = p.municipality || "";
    document.getElementById("pickup_province").value = p.province || "";
  });

</script>
</body>


</html>