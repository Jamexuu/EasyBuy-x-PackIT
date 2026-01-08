<?php

declare(strict_types=1);

session_start(); // Start session to access user ID

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

  } elseif ($action === "calculate_fare") {
    // Door-to-door removed: base + distance only
    $pRegion = (string)($_GET["pickup_region"] ?? "");
    $dRegion = (string)($_GET["drop_region"] ?? "");

    $pickupFareRegion = region_code_to_fare_region($pRegion);
    $dropFareRegion = region_code_to_fare_region($dRegion);

    $base = (int)($state["base_amount"] ?? 0);
    $dist = compute_distance_fare($pickupFareRegion, $dropFareRegion);

    $total = 0;
    $valid = false;

    if ($dist !== null) {
      $total = compute_total_fare($base, $dist, 0);
      $valid = true;
    }

    $response = [
      "valid" => $valid,
      "base" => number_format($base, 0),
      "distance" => ($dist !== null) ? number_format($dist, 0) : "--",
      "total" => ($valid) ? number_format($total, 2) : "--"
    ];

  /**
   * NEW: Single endpoint that returns BOTH:
   * - user contact (name + contact_number)
   * - user latest address (from addresses table)
   *
   * This matches what profile_seed.php “gets” from the current user.
   */
  } elseif ($action === "get_user_profile_defaults") {
    if (!isset($_SESSION['user']['id'])) {
      echo json_encode(null);
      exit;
    }

    $DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
    $DB_NAME = getenv('DB_NAME') ?: 'packit';
    $DB_USER = getenv('DB_USER') ?: 'root';
    $DB_PASS = getenv('DB_PASS') ?: '';

    try {
      $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]);

      $uid = (int)$_SESSION['user']['id'];

      // 1) Contact from users
      $stmtU = $pdo->prepare("SELECT first_name, last_name, contact_number FROM users WHERE id = :uid LIMIT 1");
      $stmtU->execute([':uid' => $uid]);
      $u = $stmtU->fetch();

      // 2) Latest address from addresses
      $stmtA = $pdo->prepare("SELECT * FROM addresses WHERE user_id = :uid ORDER BY id DESC LIMIT 1");
      $stmtA->execute([':uid' => $uid]);
      $addr = $stmtA->fetch();

      if (!$u && !$addr) {
        $response = null;
      } else {
        $contactName = $u ? trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) : '';
        $contactNumber = $u['contact_number'] ?? '';

        $response = [
          "contact" => [
            "name" => (string)$contactName,
            "contact_number" => (string)$contactNumber,
          ],
          "address" => $addr ? [
            'house' => trim(($addr['house_number'] ?? '') . ' ' . ($addr['street'] ?? '') . ' ' . ($addr['subdivision'] ?? '')),
            'barangay' => (string)($addr['barangay'] ?? ''),
            'municipality' => (string)($addr['city'] ?? ''),
            'province' => (string)($addr['province'] ?? '')
          ] : null
        ];
      }
    } catch (Exception $e) {
      $response = null;
    }
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

// NEW CONTACTS (saved in session)
$pickupContactName = (string)($state["pickup_contact_name"] ?? "");
$pickupContactNumber = (string)($state["pickup_contact_number"] ?? "");
$dropContactName = (string)($state["drop_contact_name"] ?? "");
$dropContactNumber = (string)($state["drop_contact_number"] ?? "");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Contacts
  $pickupContactName = trim((string)($_POST["pickup_contact_name"] ?? ""));
  $pickupContactNumber = trim((string)($_POST["pickup_contact_number"] ?? ""));
  $dropContactName = trim((string)($_POST["drop_contact_name"] ?? ""));
  $dropContactNumber = trim((string)($_POST["drop_contact_number"] ?? ""));

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

  if (
    $pickup["province_code"] === "" || $pickup["city_code"] === "" ||
    $drop["province_code"] === "" || $drop["city_code"] === ""
  ) {
    $error = "Please select both pickup and drop-off locations before proceeding.";
  } elseif ($pickupContactName === "" || $pickupContactNumber === "" || $dropContactName === "" || $dropContactNumber === "") {
    $error = "Please fill in pickup and recipient contact name & CP number.";
  } else {
    $pickupFareRegion = region_code_to_fare_region($pickup["region_code"]);
    $dropFareRegion = region_code_to_fare_region($drop["region_code"]);

    $_SESSION["booking"] ??= [];
    $_SESSION["booking"]["pickup_address"] = $pickup;
    $_SESSION["booking"]["drop_address"] = $drop;
    $_SESSION["booking"]["pickup_region"] = $pickupFareRegion;
    $_SESSION["booking"]["drop_region"] = $dropFareRegion;

    // Save contacts
    $_SESSION["booking"]["pickup_contact_name"] = $pickupContactName;
    $_SESSION["booking"]["pickup_contact_number"] = $pickupContactNumber;
    $_SESSION["booking"]["drop_contact_name"] = $dropContactName;
    $_SESSION["booking"]["drop_contact_number"] = $dropContactNumber;

    // Ensure door-to-door is not used anymore
    unset($_SESSION["booking"]["door_to_door"]);

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
$distanceAmount = compute_distance_fare($pickupRegion, $dropRegion);

$totalAmount = null;
if ($distanceAmount !== null) {
  $totalAmount = compute_total_fare($baseAmount, $distanceAmount, 0);
}

// Booking summary display
$userPackageDesc = (string)($state["package_desc"] ?? "");
$pkgQty = (int)($state["package_quantity"] ?? 1);
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PackIT - Address</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root { --brand-yellow: #f8e14b; --brand-yellow-dark: #e6cc32; --brand-black: #1c1c1c; }
    body { background: #f8f9fa; }
    .card-header { background: var(--brand-yellow) !important; color: var(--brand-black) !important; }
    .muted-sm { font-size: .9rem; color: #6c757d; }
    .price-tag { font-size: 2rem; font-weight: 800; color: var(--brand-black); }
  </style>
</head>

<body>
  <?php include("../components/navbar.php"); ?>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-9 col-md-11">
        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-header p-4 rounded-top-4">
            <h4 class="mb-0 fw-bold">Step 2: Pickup & Drop-off Address</h4>
            <div class="muted-sm mt-2">
              <div><strong>Vehicle:</strong> <?= h((string)($state["vehicle_label"] ?? "--")) ?></div>
              <div><strong>Package Qty:</strong> <?= (int)$pkgQty ?></div>
              <div><strong>Package Description:</strong> <?= h($userPackageDesc !== "" ? $userPackageDesc : "--") ?></div>
            </div>
          </div>

          <div class="card-body p-4">
            <?php if ($error): ?>
              <div class="alert alert-danger"><?= h($error) ?></div>
            <?php endif; ?>

            <div class="alert alert-info">
              <div class="fw-bold mb-1">Default pickup details</div>
              <div class="muted-sm mb-2">Auto-fill from your DB profile (users + addresses).</div>
              <button class="btn btn-sm btn-warning" type="button" id="useDefaultPickupBtn">
                Use my profile defaults
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

              <!-- Contacts -->
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <div class="bg-light p-3 rounded h-100">
                    <h6 class="fw-bold mb-3">Pickup Contact</h6>
                    <label class="form-label small text-muted">Name *</label>
                    <input class="form-control" name="pickup_contact_name" id="pickup_contact_name" value="<?= h($pickupContactName) ?>" required>
                    <label class="form-label small text-muted mt-2">CP Number *</label>
                    <input class="form-control" name="pickup_contact_number" id="pickup_contact_number" value="<?= h($pickupContactNumber) ?>" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="bg-light p-3 rounded h-100">
                    <h6 class="fw-bold mb-3">Recipient Contact</h6>
                    <label class="form-label small text-muted">Name *</label>
                    <input class="form-control" name="drop_contact_name" value="<?= h($dropContactName) ?>" required>
                    <label class="form-label small text-muted mt-2">CP Number *</label>
                    <input class="form-control" name="drop_contact_number" value="<?= h($dropContactNumber) ?>" required>
                  </div>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <div class="bg-light p-3 rounded h-100">
                    <h6 class="fw-bold text-primary-black mb-3">Pickup Location</h6>
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
                    <h6 class="fw-bold text-primary-black mb-3">Drop-off Location</h6>
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

              <div class="row g-3">
                <div class="col-md-7">
                  <div class="small text-muted">Fare breakdown</div>
                  <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between">
                      <span>Package base</span><strong id="disp_base">₱<?= number_format($baseAmount, 0) ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                      <span>Distance fare</span>
                      <strong id="disp_distance"><?= ($distanceAmount === null) ? "--" : "₱" . number_format($distanceAmount, 0) ?></strong>
                    </li>
                  </ul>
                </div>
                <div class="col-md-5 text-center">
                  <div class="small text-muted text-uppercase">Total</div>
                  <div class="price-tag" id="disp_total"><?= ($totalAmount === null) ? "₱--" : "₱" . number_format($totalAmount, 2) ?></div>
                  <div class="d-flex gap-2 mt-3">
                    <a class="btn btn-outline-secondary w-50" href="package.php">Back</a>
                    <button type="submit" class="btn btn-warning w-50" <?= ($totalAmount === null ? "disabled" : "") ?> id="btnNext">Next</button>
                  </div>
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include("../components/chat.php"); ?>
  <?php include("../components/footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
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

    function reset(selectEl) {
      selectEl.innerHTML = '<option value="">Select Option</option>';
      selectEl.disabled = true;
    }

    function updateHiddenName(selectEl, hiddenEl) {
      const opt = selectEl.options[selectEl.selectedIndex];
      hiddenEl.value = (opt && opt.dataset && opt.dataset.name) ? opt.dataset.name : '';
    }

    async function updateLiveFare() {
      const pRegion = document.getElementById("pickup_region_code").value;
      const dRegion = document.getElementById("drop_region_code").value;
      if (!pRegion || !dRegion) return;

      const params = new URLSearchParams({
        action: "calculate_fare",
        pickup_region: pRegion,
        drop_region: dRegion
      });

      const data = await fetchData(params.toString());
      if (data.valid) {
        document.getElementById("disp_base").textContent = "₱" + data.base;
        document.getElementById("disp_distance").textContent = "₱" + data.distance;
        document.getElementById("disp_total").textContent = "₱" + data.total;
        document.getElementById("btnNext").disabled = false;
      } else {
        document.getElementById("disp_distance").textContent = "--";
        document.getElementById("disp_total").textContent = "₱--";
        document.getElementById("btnNext").disabled = true;
      }
    }

    async function setupSelector(prefix, saved) {
      const provinceEl = document.getElementById(prefix + "_province");
      const cityEl = document.getElementById(prefix + "_city");
      const barangayEl = document.getElementById(prefix + "_barangay");

      const hiddenRegionCode = document.getElementById(prefix + "_region_code");
      const hiddenRegionName = document.getElementById(prefix + "_region_name");
      const hiddenProvince = document.getElementById(prefix + "_province_name");
      const hiddenCity = document.getElementById(prefix + "_city_name");
      const hiddenBarangay = document.getElementById(prefix + "_barangay_name");

      const provinces = await fetchData("action=get_provinces");
      populate(provinceEl, provinces, "province_code", "province_name", { regionCode: "region_code" });

      if (saved.province_code) {
        provinceEl.value = saved.province_code;
        handleProvinceChange(provinceEl.value);
      }

      provinceEl.addEventListener("change", function() {
        handleProvinceChange(this.value);
      });

      async function handleProvinceChange(provCode) {
        reset(cityEl);
        reset(barangayEl);
        updateHiddenName(provinceEl, hiddenProvince);

        if (!provCode) {
          hiddenRegionCode.value = "";
          hiddenRegionName.value = "";
          return;
        }

        const selectedOpt = provinceEl.options[provinceEl.selectedIndex];
        const regCode = selectedOpt.dataset.regionCode;
        if (regCode) {
          const regName = REGION_MAP[regCode] || regCode;
          hiddenRegionCode.value = regCode;
          hiddenRegionName.value = regName;
        }

        updateLiveFare();

        const cities = await fetchData(`action=get_cities&province_code=${encodeURIComponent(provCode)}`);
        populate(cityEl, cities, "city_code", "city_name");

        if (saved.city_code && cities.some(c => c.city_code === saved.city_code)) {
          cityEl.value = saved.city_code;
          cityEl.dispatchEvent(new Event("change"));
          saved.city_code = null;
        }
      }

      cityEl.addEventListener("change", async function() {
        const code = this.value;
        reset(barangayEl);
        updateHiddenName(this, hiddenCity);
        if (!code) return;
        const b = await fetchData(`action=get_barangays&city_code=${encodeURIComponent(code)}`);
        populate(barangayEl, b, "brgy_code", "brgy_name");
        if (saved.brgy_code) {
          barangayEl.value = saved.brgy_code;
          barangayEl.dispatchEvent(new Event("change"));
        }
      });

      barangayEl.addEventListener("change", function() {
        updateHiddenName(this, hiddenBarangay);
      });
    }

    (async function init() {
      const regionsData = await fetchData("action=get_regions");
      regionsData.forEach(r => { REGION_MAP[r.region_code] = r.region_name; });

      setupSelector("pickup", {
        province_code: "<?= h((string)($pickup["province_code"] ?? "")) ?>",
        city_code: "<?= h((string)($pickup["city_code"] ?? "")) ?>",
        brgy_code: "<?= h((string)($pickup["brgy_code"] ?? "")) ?>"
      });

      setupSelector("drop", {
        province_code: "<?= h((string)($drop["province_code"] ?? "")) ?>",
        city_code: "<?= h((string)($drop["city_code"] ?? "")) ?>",
        brgy_code: "<?= h((string)($drop["brgy_code"] ?? "")) ?>"
      });
    })();

    // Smart matching
    function normalize(str) {
      if (!str) return "";
      return str.toLowerCase()
        .replace(/\(.*\)/g, "")
        .replace("city of", "")
        .replace("municipality of", "")
        .replace("brgy", "")
        .replace("barangay", "")
        .trim();
    }

    function findOptionBySmartMatch(selectEl, searchStr) {
      if (!searchStr) return null;
      const target = normalize(searchStr);
      return Array.from(selectEl.options).find(o => {
        const optText = normalize(o.text);
        return optText === target || optText.includes(target) || target.includes(optText);
      });
    }

    // UPDATED BUTTON: fill BOTH address + contact from DB
    document.getElementById("useDefaultPickupBtn").addEventListener("click", async () => {
      try {
        const res = await fetch("?action=get_user_profile_defaults");
        const data = await res.json();

        if (!data) {
          alert("No profile defaults found (user or address missing).");
          return;
        }

        // Fill contact
        if (data.contact) {
          const name = data.contact.name || "";
          const cp = data.contact.contact_number || "";
          document.getElementById("pickup_contact_name").value = name;
          document.getElementById("pickup_contact_number").value = cp;
        }

        // Fill address
        const p = data.address;
        if (!p) {
          alert("No address found in your database profile.");
          return;
        }

        // 1. Fill House
        document.getElementById("pickup_house").value = p.house || "";

        // 2. Province
        const provSelect = document.getElementById("pickup_province");
        const provOpt = findOptionBySmartMatch(provSelect, p.province);

        if (provOpt) {
          provSelect.value = provOpt.value;
          provSelect.dispatchEvent(new Event("change"));

          // 3. City
          const waitForCities = setInterval(() => {
            const citySelect = document.getElementById("pickup_city");
            if (citySelect.options.length > 1 && !citySelect.disabled) {
              clearInterval(waitForCities);

              const cityOpt = findOptionBySmartMatch(citySelect, p.municipality);
              if (cityOpt) {
                citySelect.value = cityOpt.value;
                citySelect.dispatchEvent(new Event("change"));

                // 4. Barangay
                const waitForBrgy = setInterval(() => {
                  const brgySelect = document.getElementById("pickup_barangay");
                  if (brgySelect.options.length > 1 && !brgySelect.disabled) {
                    clearInterval(waitForBrgy);
                    const brgyOpt = findOptionBySmartMatch(brgySelect, p.barangay);
                    if (brgyOpt) {
                      brgySelect.value = brgyOpt.value;
                      brgySelect.dispatchEvent(new Event("change"));
                    }
                  }
                }, 100);
              }
            }
          }, 100);
        } else {
          alert(`Could not find province "${p.province}"`);
        }

      } catch (e) {
        console.error("Error fetching profile defaults", e);
        alert("Failed to load profile defaults.");
      }
    });
  </script>
</body>
</html>