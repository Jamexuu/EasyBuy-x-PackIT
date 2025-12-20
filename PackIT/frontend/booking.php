<!doctype html>
<html lang="en">
<head>
<<<<<<< HEAD
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
=======
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
>>>>>>> ece1dd133f460247916526a558cfbf9bccbf01ec
  <title>PackIT - Quick Book</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    body { background-color: #f8f9fa; }
<<<<<<< HEAD
    .step-container { display: none; animation: fadeIn 0.4s; }
    .step-container.active { display: block; }
    .price-tag { font-size: 2.5rem; font-weight: 800; color: #0d6efd; }
    .receipt-card { background: #fff; border: 2px dashed #ccc; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    /* OSM autocomplete dropdown */
=======
    .step-container { display: none; animation: fadeIn 0.35s; }
    .step-container.active { display: block; }
    .price-tag { font-size: 2.2rem; font-weight: 800; color: #0d6efd; }
    .receipt-card { background: #fff; border: 2px dashed #ccc; max-width:420px; margin:0 auto; }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    /* Autocomplete dropdown */
>>>>>>> ece1dd133f460247916526a558cfbf9bccbf01ec
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
<<<<<<< HEAD
    .ac-item {
      padding: .6rem .75rem;
      cursor: pointer;
      border-bottom: 1px solid rgba(0,0,0,.05);
    }
    .ac-item:last-child { border-bottom: 0; }
    .ac-item:hover, .ac-item.active { background: #f1f5ff; }
    .ac-title { font-weight: 700; }
    .ac-sub { font-size: .85rem; color: #6c757d; }
=======
    .ac-item { padding: .5rem .75rem; cursor: pointer; border-bottom: 1px solid rgba(0,0,0,.05); }
    .ac-item:hover, .ac-item.active { background: #f1f5ff; }
    .ac-title { font-weight: 700; }
    .ac-sub { font-size: .85rem; color: #6c757d; }

    /* Items UI */
    .item-card { border: 1px solid rgba(0,0,0,.06); background:#fff; padding: .75rem; border-radius: .5rem; margin-bottom:.75rem; }
    .item-actions { display:flex; gap:.5rem; align-items:center; }

    /* Vehicles */
    .vehicle-grid { display:flex; gap:.5rem; flex-wrap:wrap; }
    .vehicle-item { flex: 1 1 30%; min-width:120px; background:#fff; border:1px solid rgba(0,0,0,.06); padding:.6rem; border-radius:.6rem; text-align:center; cursor:pointer; }
    .vehicle-item.small { padding:.5rem; }
    .vehicle-item img { width:80px; height:60px; object-fit:cover; border-radius:.25rem; }
    .vehicle-item.active { box-shadow: 0 0 0 3px rgba(13,110,253,.12); border-color:#0d6efd; }

    .muted-sm { font-size:.85rem; color:#6c757d; }
    .required { color:#d00; }

    /* smaller UI tweaks */
    .small-input { font-size:.92rem; padding: .45rem .6rem; }
>>>>>>> ece1dd133f460247916526a558cfbf9bccbf01ec
  </style>
</head>

<body>
<div class="container py-5">
  <div class="row justify-content-center">
<<<<<<< HEAD
    <div class="col-lg-6 col-md-8">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-primary text-white p-4 rounded-top-4">
          <h4 class="mb-0 fw-bold"><i class="bi bi-truck"></i> Book Delivery</h4>
          <p class="mb-0 small opacity-75">Zone-based Shipping</p>
=======
    <div class="col-lg-8 col-md-10">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-primary text-white p-4 rounded-top-4">
          <h4 class="mb-0 fw-bold"><i class="bi bi-truck"></i> Book Delivery</h4>
          <p class="mb-0 small opacity-75">Multi-item, size & quantity, vehicle selection</p>
>>>>>>> ece1dd133f460247916526a558cfbf9bccbf01ec
        </div>

        <div class="card-body p-4">

<<<<<<< HEAD
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
=======
          <!-- STEP 1: Items -->
          <div id="step1" class="step-container active">
            <h5 class="mb-3 text-primary">1. Items (one product per row)</h5>
            <div id="itemsList">
              <!-- JS will populate initial item -->
            </div>

            <div class="d-flex gap-2 mb-3">
              <button class="btn btn-outline-secondary" id="addItemBtn"><i class="bi bi-plus-lg"></i> Add another item</button>
              <button class="btn btn-outline-danger" id="clearItemsBtn"><i class="bi bi-trash"></i> Clear all</button>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Vehicle suggestion</label>
              <div class="d-flex align-items-center gap-2">
                <div id="vehicleSuggestion" class="muted-sm">Enter item weights/quantities to get a suggestion.</div>
                <div id="suggestedVehicleBadge"></div>
              </div>
            </div>

            <div class="d-flex gap-2">
              <button class="btn btn-outline-secondary w-50" onclick="showStep(1)">Reset view</button>
              <button class="btn btn-primary w-50" onclick="nextStepValidateToAddress()">Next: Address &rarr;</button>
            </div>
          </div>

          <!-- STEP 2: Addresses & vehicle selection -->
          <div id="step2" class="step-container">
            <h5 class="mb-3 text-primary">2. Where (pickup & drop) & choose vehicle</h5>

            <div class="row g-3 mb-3">
              <!-- Pickup -->
              <div class="col-md-6">
                <div class="bg-light p-3 rounded">
                  <h6 class="fw-bold text-success"><i class="bi bi-geo-alt-fill"></i> Pickup</h6>

                  <div class="mb-2">
                    <label class="small text-muted">Subdivision / Village</label>
                    <input type="text" id="pickupSubdivision" class="form-control small-input" placeholder="Subdivision / Village">
                  </div>

                  <div class="mb-2">
                    <label class="small text-muted">Unit / Floor (if any)</label>
                    <input type="text" id="pickupUnit" class="form-control small-input" placeholder="e.g. 2F Unit 3">
                  </div>

                  <div class="mb-2">
                    <label class="small text-muted">Street</label>
                    <input type="text" id="pickupStreet" class="form-control small-input" placeholder="Street / Landmark">
                  </div>

                  <div class="mb-2">
                    <label class="small text-muted">Barangay</label>
                    <input type="text" id="pickupBarangay" class="form-control small-input" placeholder="Barangay">
                  </div>

                  <div class="mb-2">
                    <label class="small text-muted">City / Municipality (search) <span class="required">*</span></label>
                    <div class="ac-wrap">
                      <input type="text" id="pickupCity" class="form-control small-input fw-bold" placeholder="Type municipality/city (e.g. Tanauan)" autocomplete="off">
                      <input type="hidden" id="pickupOsmId">
                      <div id="pickupCityList" class="ac-list" role="listbox"></div>
                    </div>
                  </div>

                  <div class="mb-2">
                    <label class="small text-muted">Province</label>
                    <input type="text" id="pickupProvince" class="form-control small-input" placeholder="Province">
                  </div>

                  <div class="mb-2">
                    <label class="small text-muted">Country</label>
                    <input type="text" id="pickupCountry" class="form-control small-input" placeholder="Country" value="Philippines">
                  </div>

                  <div class="mb-2">
                    <label class="small text-muted">Postal Code</label>
                    <input type="text" id="pickupPostal" class="form-control small-input" placeholder="Postal / ZIP">
                  </div>
                </div>
              </div>

              <!-- Drop -->
              <div class="col-md-6">
                <div class="bg-light p-3 rounded">
                  <h6 class="fw-bold text-danger"><i class="bi bi-geo-alt-fill"></i> Drop-off</h6>

                  <div class="mb-2">
                    <label class="small text-muted">Subdivision / Village</label>
                    <input type="text" id="dropSubdivision" class="form-control small-input" placeholder="Subdivision / Village">
                  </div>

                  <div class="mb-2">
                    <label class="small text-muted">Unit / Floor (if any)</label>
                    <input type="text" id="dropUnit" class="form-control small-input" placeholder="e.g. 2F Unit 3">
                  </div>

                  <div class="mb-2">
                    <label class="small text-muted">Street</label>
                    <input type="text" id="dropStreet" class="form-control small-input" placeholder="Street / Landmark">
                  </div>

                  <div class="mb-2">
                    <label class="small text-muted">Barangay</label>
                    <input type="text" id="dropBarangay" class="form-control small-input" placeholder="Barangay">
                  </div>

                  <div class="mb-2">
                    <label class="small text-muted">City / Municipality (search) <span class="required">*</span></label>
                    <div class="ac-wrap">
                      <input type="text" id="dropCity" class="form-control small-input fw-bold" placeholder="Type municipality/city (e.g. Calamba)" autocomplete="off">
                      <input type="hidden" id="dropOsmId">
                      <div id="dropCityList" class="ac-list" role="listbox"></div>
                    </div>
                  </div>

                  <div class="mb-2">
                    <label class="small text-muted">Province</label>
                    <input type="text" id="dropProvince" class="form-control small-input" placeholder="Province">
                  </div>

                  <div class="mb-2">
                    <label class="small text-muted">Country</label>
                    <input type="text" id="dropCountry" class="form-control small-input" placeholder="Country" value="Philippines">
                  </div>

                  <div class="mb-2">
                    <label class="small text-muted">Postal Code</label>
                    <input type="text" id="dropPostal" class="form-control small-input" placeholder="Postal / ZIP">
                  </div>
                </div>
              </div>
            </div>

            <hr>

            <div class="mb-3">
              <label class="form-label fw-bold">Choose vehicle (or use suggestion)</label>
              <div class="vehicle-grid" id="vehicleGrid">
                <!-- JS will create the six vehicle options -->
              </div>
              <div class="small text-muted mt-2">Click a vehicle to select. Images are illustrative.</div>
>>>>>>> ece1dd133f460247916526a558cfbf9bccbf01ec
            </div>

            <div class="d-flex gap-2">
              <button class="btn btn-outline-secondary w-50" onclick="prevStep(1)">Back</button>
              <button class="btn btn-primary w-50" onclick="calculateFare()">Review Price &rarr;</button>
            </div>

            <div class="small text-muted mt-3">
<<<<<<< HEAD
              Search powered by OpenStreetMap (Nominatim). Select from suggestions so we can compute distance.
            </div>
          </div>

          <!-- STEP 3 -->
          <div id="step3" class="step-container">
            <h5 class="mb-3 text-primary">3. Confirm Booking</h5>

            <div class="text-center mb-4">
=======
              Search powered by OpenStreetMap (Nominatim). Make sure you select city suggestions so we can compute distance.
            </div>
          </div>

          <!-- STEP 3: Review & Pay -->
          <div id="step3" class="step-container">
            <h5 class="mb-3 text-primary">3. Confirm Booking & Payment</h5>

            <div class="text-center mb-3">
>>>>>>> ece1dd133f460247916526a558cfbf9bccbf01ec
              <small class="text-uppercase text-muted ls-1">Total Fare</small>
              <div class="price-tag">₱<span id="displayPrice">0.00</span></div>
              <span class="badge bg-warning text-dark" id="displayVehicle">--</span>
              <div class="small text-muted mt-2">Distance: <span id="displayDistance">--</span></div>
            </div>

<<<<<<< HEAD
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
=======
            <div class="mb-3">
              <h6 class="fw-bold">Summary</h6>
              <ul class="list-group list-group-flush small" id="summaryList" style="max-height:220px; overflow:auto;">
                <!-- Items & addresses will be populated -->
              </ul>
            </div>

            <div class="d-flex gap-2 mb-3">
              <button class="btn btn-outline-secondary w-50" onclick="prevStep(2)">Change details</button>
              <button class="btn btn-success w-50" onclick="openConfirmModal()">Confirm & Proceed to Payment</button>
            </div>

            <div id="paypal-button-wrapper" style="display:none;">
              <div id="paypal-button-container"></div>
              <div class="small text-muted mt-2">Pay with PayPal (sandbox). For production, use live keys and server-side validation.</div>
            </div>

            <div class="small text-muted mt-3">
              Note: We'll ask for final confirmation before charging.
            </div>
          </div>

          <!-- STEP 4: Paid / Receipt -->
>>>>>>> ece1dd133f460247916526a558cfbf9bccbf01ec
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
<<<<<<< HEAD
=======
</div>

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Booking</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="confirmSummary"></div>
        <div class="mt-3 text-end">
          <strong class="me-3">Total: ₱<span id="confirmTotal">0.00</span></strong>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Edit</button>
        <button class="btn btn-success" id="confirmAndPayBtn">Confirm & Pay</button>
      </div>
    </div>
  </div>
>>>>>>> ece1dd133f460247916526a558cfbf9bccbf01ec
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://www.paypal.com/sdk/js?client-id=sb&currency=PHP&enable-funding=venmo"></script>

<script>
<<<<<<< HEAD
  // --- VARIABLES ---
  let currentStep = 1;
  let finalFare = 0;
  let finalDistanceKm = null;

  // IMPORTANT: store coords from Nominatim selection
  let pickupCoords = null; // {lat, lng}
  let dropCoords = null;   // {lat, lng}

  // --- NAVIGATION ---
=======
  // --- STATE ---
  let currentStep = 1;
  let finalFare = 0;
  let finalDistanceKm = null;
  let pickupCoords = null;
  let dropCoords = null;
  let items = []; // array of {id, desc, qty, size, weight}
  let vehicleSelected = null;
  let paypalRendered = false;

  const VEHICLES = [
    { key: "Motorcycle", label: "Motorcycle", img: "https://via.placeholder.com/160x120?text=Motorcycle" },
    { key: "ToktokTricycle", label: "Toktok / Tricycle", img: "https://via.placeholder.com/160x120?text=Toktok" },
    { key: "Sedan", label: "Sedan / Car", img: "https://via.placeholder.com/160x120?text=Sedan" },
    { key: "SUV", label: "SUV", img: "https://via.placeholder.com/160x120?text=SUV" },
    { key: "Pickup", label: "Pickup", img: "https://via.placeholder.com/160x120?text=Pickup" },
    { key: "Truck", label: "Truck", img: "https://via.placeholder.com/160x120?text=Truck" }
  ];

  // --- UI NAV ---
>>>>>>> ece1dd133f460247916526a558cfbf9bccbf01ec
  function showStep(step) {
    document.querySelectorAll('.step-container').forEach(el => el.classList.remove('active'));
    document.getElementById(`step${step}`).classList.add('active');
    currentStep = step;
  }

<<<<<<< HEAD
  function nextStep(step) {
    if (step === 2 && document.getElementById('itemDesc').value.trim() === "") {
      alert("Please enter an item description.");
      return;
    }
    showStep(step);
=======
  function nextStepValidateToAddress() {
    if (items.length === 0) {
      alert("Please add at least one item.");
      return;
    }
    // Require each item to have valid fields
    for (const it of items) {
      if (!it.desc || it.qty < 1 || !it.size || isNaN(it.weight) || it.weight <= 0) {
        alert("Please make sure each item has description, quantity (>=1), size, and a positive weight.");
        return;
      }
    }
    showStep(2);
>>>>>>> ece1dd133f460247916526a558cfbf9bccbf01ec
  }

  function prevStep(step) { showStep(step); }

<<<<<<< HEAD
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
=======
  // --- Items management ---
  function addItem(initial) {
    const id = Math.floor(Math.random() * 1000000);
    const item = Object.assign({
      id,
      desc: initial?.desc || "",
      qty: initial?.qty || 1,
      size: initial?.size || "Small",
      weight: initial?.weight || 0
    }, initial || {});
    items.push(item);
    renderItems();
    updateVehicleSuggestion();
  }

  function removeItem(id) {
    items = items.filter(i => i.id !== id);
    renderItems();
    updateVehicleSuggestion();
  }

  function updateItemField(id, field, value) {
    const it = items.find(x => x.id === id);
    if (!it) return;
    if (field === "qty") it.qty = parseInt(value) || 0;
    else if (field === "weight") it.weight = parseFloat(value) || 0;
    else it[field] = value;
    renderItems(false);
    updateVehicleSuggestion();
  }

  function clearAllItems() {
    if (!confirm("Clear all items?")) return;
    items = [];
    renderItems();
    updateVehicleSuggestion();
  }

  function renderItems(scroll=true) {
    const container = document.getElementById('itemsList');
    container.innerHTML = "";
    if (items.length === 0) {
      // add an empty default row
      addItem();
      return;
    }

    items.forEach(it => {
      const div = document.createElement('div');
      div.className = "item-card";
      div.innerHTML = `
        <div class="row g-2 align-items-center">
          <div class="col-md-5">
            <label class="form-label mb-1 small">Description</label>
            <input type="text" class="form-control form-control-sm" value="${escapeHtml(it.desc)}" onchange="updateItemField(${it.id}, 'desc', this.value)" placeholder="e.g. Documents, Laptop, Cake">
          </div>
          <div class="col-md-2">
            <label class="form-label mb-1 small">Qty</label>
            <input type="number" min="1" class="form-control form-control-sm" value="${it.qty}" onchange="updateItemField(${it.id}, 'qty', this.value)">
          </div>
          <div class="col-md-2">
            <label class="form-label mb-1 small">Size</label>
            <select class="form-select form-select-sm" onchange="updateItemField(${it.id}, 'size', this.value)">
              <option ${it.size==='Small'?'selected':''}>Small</option>
              <option ${it.size==='Medium'?'selected':''}>Medium</option>
              <option ${it.size==='Large'?'selected':''}>Large</option>
              <option ${it.size==='XXL'?'selected':''}>XXL</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label mb-1 small">Weight (kg)</label>
            <input type="number" min="0" step="0.1" class="form-control form-control-sm" value="${it.weight}" onchange="updateItemField(${it.id}, 'weight', this.value)">
          </div>
          <div class="col-md-1 text-end">
            <label class="form-label mb-1 small">&nbsp;</label>
            <div class="d-grid">
              <button class="btn btn-sm btn-outline-danger" onclick="removeItem(${it.id})" title="Remove item"><i class="bi bi-trash"></i></button>
            </div>
          </div>
        </div>
      `;
      container.appendChild(div);
    });

    if (scroll) {
      container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  // helpers
  function escapeHtml(s) { return (s+'').replace(/[&<>"']/g, function(m){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];}); }

  document.getElementById('addItemBtn').addEventListener('click', () => addItem());
  document.getElementById('clearItemsBtn').addEventListener('click', () => clearAllItems());

  // --- Vehicle UI ---
  function renderVehicleGrid() {
    const grid = document.getElementById('vehicleGrid');
    grid.innerHTML = '';
    VEHICLES.forEach(v => {
      const div = document.createElement('div');
      div.className = 'vehicle-item small';
      div.setAttribute('data-key', v.key);
      div.innerHTML = `
        <img src="${v.img}" alt="${v.label}">
        <div class="fw-bold mt-2" style="font-size:.95rem">${v.label}</div>
      `;
      div.addEventListener('click', () => {
        vehicleSelected = v.key;
        document.querySelectorAll('.vehicle-item').forEach(x => x.classList.remove('active'));
        div.classList.add('active');
        document.getElementById('displayVehicle').innerText = v.label;
      });
      grid.appendChild(div);
    });
  }

  function updateVehicleSuggestion() {
    const totalWeight = items.reduce((s,i) => s + (Number(i.weight)||0) * (Number(i.qty)||0), 0);
    const badge = document.getElementById('suggestedVehicleBadge');
    const vs = document.getElementById('vehicleSuggestion');

    let suggestion = "Motorcycle";
    if (totalWeight > 150) suggestion = "Truck";
    else if (totalWeight > 80) suggestion = "Pickup";
    else if (totalWeight > 40) suggestion = "SUV";
    else if (totalWeight > 20) suggestion = "Sedan";
    else if (totalWeight > 10) suggestion = "ToktokTricycle";
    else suggestion = "Motorcycle";

    vs.innerHTML = `Total weight: <strong>${totalWeight.toFixed(2)} kg</strong>`;
    badge.innerHTML = `<span class="badge bg-info text-dark">${suggestion}</span>`;

    // Preselect suggested vehicle only if none selected
    if (!vehicleSelected) {
      vehicleSelected = suggestion;
      // reflect selection in UI
      document.querySelectorAll('.vehicle-item').forEach(x => {
        x.classList.toggle('active', x.dataset.key === suggestion);
      });
      document.getElementById('displayVehicle').innerText = suggestion;
    }
  }

  // --- OSM AUTOCOMPLETE (same as your original but adapted) ---
>>>>>>> ece1dd133f460247916526a558cfbf9bccbf01ec
  function debounce(fn, wait = 350) {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), wait);
<<<<<<< HEAD
=======
    };
  }

  function formatMunicipalityLabel(item) {
    const a = item.address || {};
    const cityLike = a.city || a.town || a.municipality || a.village || a.county || "";
    const province = a.state || a.region || "";
    const country = a.country || "";
    const title = cityLike || item.name || (item.display_name||"").split(",")[0].trim();
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

    const res = await fetch(url.toString(), { headers: { "Accept": "application/json" }});
    if (!res.ok) return [];
    const data = await res.json();
    const allowed = new Set(["city","town","village","municipality","administrative","county","suburb"]);
    return data.filter(item => allowed.has(item.type) || allowed.has(item.addresstype));
  }

  function setupOsmAutocomplete({ inputId, listId, hiddenId, coordsSetter }) {
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
        div.innerHTML = `<div class="ac-title">${title}</div><div class="ac-sub">${sub}</div>`;
        div.addEventListener("mousedown", (e) => { e.preventDefault(); choose(idx); });
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
      const lat = parseFloat(item.lat);
      const lng = parseFloat(item.lon);
      if (Number.isFinite(lat) && Number.isFinite(lng) && typeof coordsSetter === 'function') {
        coordsSetter({ lat, lng });
      }
      closeList();
    }

    const doSearch = debounce(async () => {
      hidden.value = "";
      if (coordsSetter === setPickupCoords) pickupCoords = null;
      if (coordsSetter === setDropCoords) dropCoords = null;

      try {
        const results = await searchPHMunicipalities(input.value);
        render(results);
      } catch (err) {
        closeList();
        console.error("Nominatim search error:", err);
      }
    }, 350);

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

  function setPickupCoords(c) { pickupCoords = c; }
  function setDropCoords(c) { dropCoords = c; }

  // --- FARE CALCULATION ---
  async function calculateFare() {
    // Basic validation
    if (items.length === 0) { alert("Add at least one item."); showStep(1); return; }
    for (const it of items) {
      if (!it.desc || it.qty < 1 || !it.size || isNaN(it.weight) || it.weight <= 0) {
        alert("Please fill all item details correctly.");
        return;
      }
    }

    if (!pickupCoords || !dropCoords) {
      alert("Please SELECT both pickup and drop-off cities from suggestions so distance can be calculated.");
      return;
    }

    if (!vehicleSelected) {
      alert("Please select a vehicle.");
      return;
    }

    // Prepare params
    const payload = {
      p_lat: pickupCoords.lat,
      p_lng: pickupCoords.lng,
      d_lat: dropCoords.lat,
      d_lng: dropCoords.lng,
      vehicle: vehicleSelected,
      total_weight: items.reduce((s,i) => s + (Number(i.weight)||0) * (Number(i.qty)||0), 0),
      items: items.map(i => ({ desc: i.desc, qty: i.qty, size: i.size, weight: i.weight }))
>>>>>>> ece1dd133f460247916526a558cfbf9bccbf01ec
    };
  }

<<<<<<< HEAD
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
=======
    // We'll send items as encoded JSON in GET param (keeps compatibility with simple PHP endpoints).
    const url = new URL("calculate_fare.php", window.location.href);
    url.searchParams.set("p_lat", payload.p_lat);
    url.searchParams.set("p_lng", payload.p_lng);
    url.searchParams.set("d_lat", payload.d_lat);
    url.searchParams.set("d_lng", payload.d_lng);
    url.searchParams.set("vehicle", payload.vehicle);
    url.searchParams.set("total_weight", payload.total_weight);
    url.searchParams.set("items", encodeURIComponent(JSON.stringify(payload.items)));

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

    // Display
    document.getElementById('displayPrice').innerText = finalFare.toFixed(2);
    document.getElementById('displayVehicle').innerText = vehicleSelected;
    document.getElementById('displayDistance').innerText = `${finalDistanceKm} km`;

    // Build summary list
    const summary = document.getElementById('summaryList');
    summary.innerHTML = "";
    items.forEach((it, idx) => {
      const li = document.createElement('li');
      li.className = "list-group-item d-flex justify-content-between";
      li.innerHTML = `<div><strong>${escapeHtml(it.desc)}</strong><div class="muted-sm">Size: ${it.size} · Qty: ${it.qty} · ${it.weight} kg each</div></div><div class="text-end">Subtotal<br><strong>${(it.qty * 0).toFixed(2)}</strong></div>`;
      // note: per-item pricing not implemented client-side; server gives total price
      summary.appendChild(li);
    });

    // Addresses summary
    const routeLi = document.createElement('li');
    routeLi.className = "list-group-item";
    routeLi.innerHTML = `<div><strong>Pickup</strong><div class="muted-sm">${formatFullAddress('pickup')}</div></div><hr class="my-2"><div><strong>Drop</strong><div class="muted-sm">${formatFullAddress('drop')}</div></div>`;
    summary.appendChild(routeLi);

    nextStep(3);
  }

  function formatFullAddress(prefix) {
    const parts = [
      document.getElementById(`${prefix}Subdivision`).value,
      document.getElementById(`${prefix}Unit`).value,
      document.getElementById(`${prefix}Street`).value,
      document.getElementById(`${prefix}Barangay`).value,
      document.getElementById(`${prefix}City`).value,
      document.getElementById(`${prefix}Province`).value,
      document.getElementById(`${prefix}Country`).value,
      document.getElementById(`${prefix}Postal`).value
    ].filter(Boolean);
    return parts.join(", ");
  }

  // --- CONFIRM modal & PAYPAL ---
  function openConfirmModal() {
    // Prepare modal summary
    const summaryContainer = document.getElementById('confirmSummary');
    summaryContainer.innerHTML = "";

    const ul = document.createElement('ul');
    ul.className = "list-group mb-2";
    items.forEach(it => {
      const li = document.createElement('li');
      li.className = "list-group-item d-flex justify-content-between align-items-center";
      li.innerHTML = `<div><strong>${escapeHtml(it.desc)}</strong><div class="muted-sm">${it.size} · Qty ${it.qty} · ${it.weight} kg each</div></div><span class="text-muted">—</span>`;
      ul.appendChild(li);
    });
    summaryContainer.appendChild(ul);

    // route
    const route = document.createElement('div');
    route.innerHTML = `<div class="mb-2"><strong>Pickup:</strong> ${escapeHtml(formatFullAddress('pickup'))}</div><div><strong>Drop:</strong> ${escapeHtml(formatFullAddress('drop'))}</div><div class="mt-2"><strong>Vehicle:</strong> ${vehicleSelected}</div>`;
    summaryContainer.appendChild(route);

    document.getElementById('confirmTotal').innerText = finalFare.toFixed(2);

    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();

    document.getElementById('confirmAndPayBtn').onclick = () => {
      modal.hide();
      // Reveal PayPal buttons and render them (if not already)
      document.getElementById('paypal-button-wrapper').style.display = 'block';
      if (!paypalRendered) renderPaypalButtons();
      // scroll to PayPal
      setTimeout(() => {
        document.getElementById('paypal-button-wrapper').scrollIntoView({ behavior: 'smooth' });
      }, 200);
    };
  }

  function renderPaypalButtons() {
    paypalRendered = true;
    paypal.Buttons({
      createOrder: function(data, actions) {
        // Server-side order creation would be more secure; this is client-side for demo/sandbox purposes.
        return actions.order.create({
          purchase_units: [{ amount: { value: finalFare.toFixed(2) } }]
        });
      },
      onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
          // Show receipt step
          document.getElementById('receiptId').innerText = Math.floor(Math.random() * 100000);
          document.getElementById('recPickup').innerText = formatFullAddress('pickup');
          document.getElementById('recDrop').innerText = formatFullAddress('drop');
          document.getElementById('recAmount').innerText = finalFare.toFixed(2);
          showStep(4);

          // Optionally: send booking to server to record the order (not implemented here)
        });
      },
      onError: function(err) {
        console.error("PayPal error", err);
        alert("Payment failed or was cancelled. Please try again.");
      }
    }).render('#paypal-button-container');
  }

  // --- bootstrap helpers & init ---
  window.addEventListener('load', () => {
    renderVehicleGrid();
    // start with one blank item
    if (items.length === 0) addItem();

    setupOsmAutocomplete({ inputId: "pickupCity", listId: "pickupCityList", hiddenId: "pickupOsmId", coordsSetter: setPickupCoords });
    setupOsmAutocomplete({ inputId: "dropCity", listId: "dropCityList", hiddenId: "dropOsmId", coordsSetter: setDropCoords });

    // small accessibility helper: fill displayVehicle from initial or suggested
    document.getElementById('displayVehicle').innerText = vehicleSelected || '';
  });

  // Expose prevStep for buttons
  window.prevStep = prevStep;
  window.nextStep = function(s) { showStep(s); };

  // For debugging: you can call window.getState() in console
  window.getState = () => ({ items, pickupCoords, dropCoords, vehicleSelected, finalFare, finalDistanceKm });
>>>>>>> ece1dd133f460247916526a558cfbf9bccbf01ec

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