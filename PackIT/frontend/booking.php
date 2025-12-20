<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PackIT - Quick Book</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    body { background-color: #f8f9fa; }
    .step-container { display: none; animation: fadeIn 0.4s; }
    .step-container.active { display: block; }
    .price-tag { font-size: 2.5rem; font-weight: 800; color: #0d6efd; }
    .receipt-card { background: #fff; border: 2px dashed #ccc; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    /* OSM autocomplete dropdown */
    .ac-wrap { position: relative; }
    .ac-list {
      position: absolute;
      z-index: 1050;
      top: 100%;
      left: 0;
      right: 0;
      background: #fff;
      border: 1px solid rgba(0,0,0,.15);
      border-radius: .5rem;
      box-shadow: 0 .5rem 1rem rgba(0,0,0,.1);
      max-height: 280px;
      overflow: auto;
      margin-top: .25rem;
      display: none;
    }
    .ac-item {
      padding: .6rem .75rem;
      cursor: pointer;
      border-bottom: 1px solid rgba(0,0,0,.05);
    }
    .ac-item:last-child { border-bottom: 0; }
    .ac-item:hover, .ac-item.active { background: #f1f5ff; }
    .ac-title { font-weight: 700; }
    .ac-sub { font-size: .85rem; color: #6c757d; }
  </style>
</head>

<body>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-primary text-white p-4 rounded-top-4">
          <h4 class="mb-0 fw-bold"><i class="bi bi-truck"></i> Book Delivery</h4>
          <p class="mb-0 small opacity-75">Zone-based Shipping</p>
        </div>

        <div class="card-body p-4">

          <!-- STEP 1 -->
          <div id="step1" class="step-container active">
            <h5 class="mb-3 text-primary">1. What are we shipping?</h5>

            <div class="mb-3">
              <label class="form-label fw-bold">Item Description</label>
              <input type="text" id="itemDesc" class="form-control" placeholder="e.g. Documents, Laptop, Cake">
            </div>

            <div class="row mb-3">
              <div class="col-6">
                <label class="form-label fw-bold">Weight (kg)</label>
                <input type="number" id="itemWeight" class="form-control" placeholder="kg" oninput="recommendVehicle()">
              </div>
              <div class="col-6">
                <label class="form-label fw-bold">Vehicle</label>
                <select id="vehicleType" class="form-select">
                  <option value="Motorcycle">Motorcycle</option>
                  <option value="Car">Sedan / Car</option>
                </select>
              </div>
            </div>

            <div id="vehicleNote" class="small text-muted mb-4">Enter weight for suggestion.</div>

            <button class="btn btn-primary w-100 py-2" onclick="nextStep(2)">Next: Address &rarr;</button>
          </div>

          <!-- STEP 2 -->
          <div id="step2" class="step-container">
            <h5 class="mb-3 text-primary">2. Where to?</h5>

            <div class="bg-light p-3 rounded mb-3">
              <h6 class="fw-bold text-success"><i class="bi bi-geo-alt-fill"></i> Pickup</h6>

              <div class="mb-2">
                <label class="small text-muted">Street / Unit / Landmark</label>
                <input type="text" id="pickupAddress" class="form-control" placeholder="e.g. Blk 5 Lot 2, Santa Maria Village">
              </div>

              <div class="ac-wrap">
                <label class="small text-muted">City / Municipality (search)</label>
                <input type="text" id="pickupCity" class="form-control fw-bold"
                       placeholder="Type municipality/city (e.g. Tanauan, Lipa)">
                <input type="hidden" id="pickupOsmId">
                <div id="pickupCityList" class="ac-list" role="listbox"></div>
              </div>
            </div>

            <div class="bg-light p-3 rounded mb-4">
              <h6 class="fw-bold text-danger"><i class="bi bi-geo-alt-fill"></i> Drop-off</h6>

              <div class="mb-2">
                <label class="small text-muted">Street / Unit / Landmark</label>
                <input type="text" id="dropAddress" class="form-control" placeholder="e.g. First Industrial Park, Gate 1">
              </div>

              <div class="ac-wrap">
                <label class="small text-muted">City / Municipality (search)</label>
                <input type="text" id="dropCity" class="form-control fw-bold"
                       placeholder="Type municipality/city (e.g. Santo Tomas, Calamba)">
                <input type="hidden" id="dropOsmId">
                <div id="dropCityList" class="ac-list" role="listbox"></div>
              </div>
            </div>

            <div class="d-flex gap-2">
              <button class="btn btn-outline-secondary w-50" onclick="prevStep(1)">Back</button>
              <button class="btn btn-primary w-50" onclick="calculateFare()">Review Price &rarr;</button>
            </div>

            <div class="small text-muted mt-3">
              Search powered by OpenStreetMap (Nominatim). Select from suggestions so we can compute distance.
            </div>
          </div>

          <!-- STEP 3 -->
          <div id="step3" class="step-container">
            <h5 class="mb-3 text-primary">3. Confirm Booking</h5>

            <div class="text-center mb-4">
              <small class="text-uppercase text-muted ls-1">Total Fare</small>
              <div class="price-tag">₱<span id="displayPrice">0.00</span></div>
              <span class="badge bg-warning text-dark" id="displayVehicle">--</span>
              <div class="small text-muted mt-2">Distance: <span id="displayDistance">--</span></div>
            </div>

            <ul class="list-group list-group-flush mb-4 small">
              <li class="list-group-item d-flex justify-content-between">
                <span class="text-muted">Route:</span>
                <strong><span id="summRoute">--</span></strong>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span class="text-muted">Item:</span>
                <strong><span id="summItem">--</span></strong>
              </li>
            </ul>

            <div id="paypal-button-container"></div>

            <button class="btn btn-link text-muted w-100 mt-2 text-decoration-none" onclick="prevStep(2)">Change details</button>
          </div>

          <!-- STEP 4 -->
          <div id="step4" class="step-container text-center">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
            <h3 class="fw-bold mt-2">Booking Paid!</h3>
            <p class="text-muted mb-4">A rider is being assigned to your location.</p>

            <div class="receipt-card p-3 text-start mx-auto rounded">
              <p class="mb-1 small text-muted">RECEIPT #<span id="receiptId">000</span></p>
              <hr class="my-2">
              <div class="d-flex justify-content-between mb-1">
                <span>Pickup:</span>
                <span class="fw-bold text-end" id="recPickup">--</span>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span>Drop-off:</span>
                <span class="fw-bold text-end" id="recDrop">--</span>
              </div>
              <div class="d-flex justify-content-between mt-3">
                <span class="fw-bold">AMOUNT PAID</span>
                <span class="fw-bold text-primary">₱<span id="recAmount">0.00</span></span>
              </div>
            </div>

            <a href="booking.php" class="btn btn-primary mt-4 px-4">New Booking</a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://www.paypal.com/sdk/js?client-id=sb&currency=PHP&enable-funding=venmo"></script>

<script>
  // --- VARIABLES ---
  let currentStep = 1;
  let finalFare = 0;
  let finalDistanceKm = null;

  // IMPORTANT: store coords from Nominatim selection
  let pickupCoords = null; // {lat, lng}
  let dropCoords = null;   // {lat, lng}

  // --- NAVIGATION ---
  function showStep(step) {
    document.querySelectorAll('.step-container').forEach(el => el.classList.remove('active'));
    document.getElementById(`step${step}`).classList.add('active');
    currentStep = step;
  }

  function nextStep(step) {
    if (step === 2 && document.getElementById('itemDesc').value.trim() === "") {
      alert("Please enter an item description.");
      return;
    }
    showStep(step);
  }

  function prevStep(step) { showStep(step); }

  // --- VEHICLE LOGIC ---
  function recommendVehicle() {
    let weight = parseFloat(document.getElementById('itemWeight').value);
    let select = document.getElementById('vehicleType');
    let note = document.getElementById('vehicleNote');

    if (!isNaN(weight)) {
      if (weight > 10) {
        select.value = "Car";
        note.innerHTML = `<span class="text-danger"><i class="bi bi-info-circle"></i> Heavy item (&gt;10kg). Car selected.</span>`;
      } else {
        select.value = "Motorcycle";
        note.innerHTML = `<span class="text-success"><i class="bi bi-check"></i> Standard weight. Motorcycle available.</span>`;
      }
    } else {
      note.innerHTML = `Enter weight for suggestion.`;
    }
  }

  // -------------------------
  // OSM NOMINATIM AUTOCOMPLETE (NO KEY)
  // -------------------------
  function debounce(fn, wait = 350) {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), wait);
    };
  }

  function formatMunicipalityLabel(item) {
    const a = item.address || {};
    const cityLike = a.city || a.town || a.municipality || a.village || a.county || "";
    const province = a.state || a.region || "";
    const country = a.country || "";
    const title = cityLike || item.name || item.display_name.split(",")[0].trim();
    const sub = [province, country].filter(Boolean).join(", ");
    return { title, sub };
  }

  async function searchPHMunicipalities(query) {
    const q = query.trim();
    if (q.length < 2) return [];

    const url = new URL("https://nominatim.openstreetmap.org/search");
    url.searchParams.set("format", "jsonv2");
    url.searchParams.set("addressdetails", "1");
    url.searchParams.set("limit", "8");
    url.searchParams.set("countrycodes", "ph");
    url.searchParams.set("accept-language", "en");
    url.searchParams.set("q", q);

    const res = await fetch(url.toString(), { headers: { "Accept": "application/json" } });
    if (!res.ok) return [];

    const data = await res.json();
    const allowed = new Set(["city","town","village","municipality","administrative","county","suburb"]);
    return data.filter(item => allowed.has(item.type) || allowed.has(item.addresstype));
  }

  function setupOsmAutocomplete({ inputId, listId, hiddenId }) {
    const input = document.getElementById(inputId);
    const list = document.getElementById(listId);
    const hidden = document.getElementById(hiddenId);

    let currentItems = [];
    let activeIndex = -1;

    function closeList() {
      list.style.display = "none";
      list.innerHTML = "";
      activeIndex = -1;
    }

    function openList() {
      if (list.innerHTML.trim() !== "") list.style.display = "block";
    }

    function render(items) {
      currentItems = items;
      list.innerHTML = "";

      if (!items.length) { closeList(); return; }

      items.forEach((item, idx) => {
        const { title, sub } = formatMunicipalityLabel(item);

        const div = document.createElement("div");
        div.className = "ac-item";
        div.setAttribute("role", "option");
        div.innerHTML = `
          <div class="ac-title">${title}</div>
          <div class="ac-sub">${sub}</div>
        `;

        div.addEventListener("mousedown", (e) => {
          e.preventDefault();
          choose(idx);
        });

        list.appendChild(div);
      });

      openList();
    }

    function setActive(newIndex) {
      const children = Array.from(list.children);
      children.forEach(c => c.classList.remove("active"));
      activeIndex = newIndex;

      if (activeIndex >= 0 && activeIndex < children.length) {
        children[activeIndex].classList.add("active");
      }
    }

    function choose(idx) {
      const item = currentItems[idx];
      if (!item) return;

      const { title } = formatMunicipalityLabel(item);
      input.value = title;
      hidden.value = item.place_id || item.osm_id || "";

      // SAVE COORDINATES (THIS IS THE IMPORTANT FIX)
      const lat = parseFloat(item.lat);
      const lng = parseFloat(item.lon);

      if (Number.isFinite(lat) && Number.isFinite(lng)) {
        if (inputId === "pickupCity") pickupCoords = { lat, lng };
        if (inputId === "dropCity") dropCoords = { lat, lng };
      }

      closeList();
    }

    const doSearch = debounce(async () => {
      hidden.value = "";
      // clear coords if user edits text after selecting
      if (inputId === "pickupCity") pickupCoords = null;
      if (inputId === "dropCity") dropCoords = null;

      try {
        const results = await searchPHMunicipalities(input.value);
        render(results);
      } catch (err) {
        closeList();
        console.error("Nominatim search error:", err);
      }
    }, 400);

    input.addEventListener("input", doSearch);

    input.addEventListener("keydown", (e) => {
      const children = Array.from(list.children);
      if (list.style.display !== "block") return;

      if (e.key === "ArrowDown") { e.preventDefault(); setActive(Math.min(activeIndex + 1, children.length - 1)); }
      else if (e.key === "ArrowUp") { e.preventDefault(); setActive(Math.max(activeIndex - 1, 0)); }
      else if (e.key === "Enter") {
        if (activeIndex >= 0) { e.preventDefault(); choose(activeIndex); }
      } else if (e.key === "Escape") closeList();
    });

    input.addEventListener("blur", () => setTimeout(closeList, 120));
    document.addEventListener("click", (e) => {
      if (!list.contains(e.target) && e.target !== input) closeList();
    });
  }

  window.addEventListener("load", () => {
    setupOsmAutocomplete({ inputId: "pickupCity", listId: "pickupCityList", hiddenId: "pickupOsmId" });
    setupOsmAutocomplete({ inputId: "dropCity", listId: "dropCityList", hiddenId: "dropOsmId" });
  });

  // --- FARE CALCULATION (FIXED: CALLS calculate_fare.php) ---
  async function calculateFare() {
    const itemDesc = document.getElementById('itemDesc').value.trim();
    const pickupCity = document.getElementById('pickupCity').value.trim();
    const dropCity = document.getElementById('dropCity').value.trim();
    const vehicle = document.getElementById('vehicleType').value;

    if (!itemDesc) { alert("Please enter an item description."); showStep(1); return; }

    // Require selection from suggestions (so coords exist)
    if (!pickupCoords || !dropCoords) {
      alert("Please SELECT both pickup and drop-off from the suggestions so distance can be calculated.");
      return;
    }

    // Call your PHP endpoint
    const url = new URL("calculate_fare.php", window.location.href);
    url.searchParams.set("p_lat", pickupCoords.lat);
    url.searchParams.set("p_lng", pickupCoords.lng);
    url.searchParams.set("d_lat", dropCoords.lat);
    url.searchParams.set("d_lng", dropCoords.lng);
    url.searchParams.set("vehicle", vehicle);

    let data;
    try {
      const res = await fetch(url.toString(), { method: "GET" });
      data = await res.json();
    } catch (e) {
      alert("Network error while calculating fare.");
      return;
    }

    if (!data || data.status !== "success") {
      alert((data && data.message) ? data.message : "Fare calculation failed.");
      return;
    }

    finalFare = parseFloat(data.price);
    finalDistanceKm = data.distance_km;

    // Display Data
    document.getElementById('displayPrice').innerText = finalFare.toFixed(2);
    document.getElementById('displayVehicle').innerText = vehicle;
    document.getElementById('displayDistance').innerText = `${finalDistanceKm} km`;
    document.getElementById('summItem').innerText = itemDesc;
    document.getElementById('summRoute').innerText = `${pickupCity} TO ${dropCity}`;

    nextStep(3);
  }

  // --- PAYPAL ---
  paypal.Buttons({
    createOrder: function(data, actions) {
      return actions.order.create({
        purchase_units: [{ amount: { value: finalFare.toFixed(2) } }]
      });
    },
    onApprove: function(data, actions) {
      return actions.order.capture().then(function(details) {
        document.getElementById('receiptId').innerText = Math.floor(Math.random() * 10000);
        document.getElementById('recPickup').innerText = document.getElementById('pickupCity').value;
        document.getElementById('recDrop').innerText = document.getElementById('dropCity').value;
        document.getElementById('recAmount').innerText = finalFare.toFixed(2);

        showStep(4);
      });
    }
  }).render('#paypal-button-container');
</script>
</body>
</html>