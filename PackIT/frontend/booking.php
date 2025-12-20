<!--New Code-->
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

                    <div id="step2" class="step-container">
                        <h5 class="mb-3 text-primary">2. Where to?</h5>
                        
                        <div class="bg-light p-3 rounded mb-3">
                            <h6 class="fw-bold text-success"><i class="bi bi-geo-alt-fill"></i> Pickup</h6>
                            <div class="mb-2">
                                <label class="small text-muted">Street / Unit / Landmark</label>
                                <input type="text" id="pickupAddress" class="form-control" placeholder="e.g. Blk 5 Lot 2, Santa Maria Village">
                            </div>
                            <div>
                                <label class="small text-muted">City / Municipality</label>
                                <select id="pickupCity" class="form-select fw-bold">
                                    <option value="Santo Tomas">Santo Tomas</option>
                                    <option value="Tanauan">Tanauan</option>
                                    <option value="Malvar">Malvar</option>
                                    <option value="Lipa">Lipa City</option>
                                    <option value="Calamba">Calamba (Laguna)</option>
                                </select>
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded mb-4">
                            <h6 class="fw-bold text-danger"><i class="bi bi-geo-alt-fill"></i> Drop-off</h6>
                            <div class="mb-2">
                                <label class="small text-muted">Street / Unit / Landmark</label>
                                <input type="text" id="dropAddress" class="form-control" placeholder="e.g. First Industrial Park, Gate 1">
                            </div>
                            <div>
                                <label class="small text-muted">City / Municipality</label>
                                <select id="dropCity" class="form-select fw-bold">
                                    <option value="Santo Tomas">Santo Tomas</option>
                                    <option value="Tanauan">Tanauan</option>
                                    <option value="Malvar">Malvar</option>
                                    <option value="Lipa">Lipa City</option>
                                    <option value="Calamba">Calamba (Laguna)</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary w-50" onclick="prevStep(1)">Back</button>
                            <button class="btn btn-primary w-50" onclick="calculateFare()">Review Price &rarr;</button>
                        </div>
                    </div>

                    <div id="step3" class="step-container">
                        <h5 class="mb-3 text-primary">3. Confirm Booking</h5>
                        
                        <div class="text-center mb-4">
                            <small class="text-uppercase text-muted ls-1">Total Fare</small>
                            <div class="price-tag">₱<span id="displayPrice">0.00</span></div>
                            <span class="badge bg-warning text-dark" id="displayVehicle">--</span>
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

    // --- PRICING MATRIX (Edit these prices) ---
    const BASE_RATES = {
        'Motorcycle': 50,
        'Car': 150
    };

    const CROSS_CITY_FEE = {
        'Motorcycle': 30, // Add 30 pesos for every city crossing?
        'Car': 70
    };

    // --- NAVIGATION ---
    function showStep(step) {
        document.querySelectorAll('.step-container').forEach(el => el.classList.remove('active'));
        document.getElementById(`step${step}`).classList.add('active');
        currentStep = step;
    }
    
    function nextStep(step) {
        if(step === 2 && document.getElementById('itemDesc').value === "") {
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
                note.innerHTML = `<span class="text-danger"><i class="bi bi-info-circle"></i> Heavy item (>10kg). Car selected.</span>`;
            } else {
                select.value = "Motorcycle";
                note.innerHTML = `<span class="text-success"><i class="bi bi-check"></i> Standard weight. Motorcycle available.</span>`;
            }
        }
    }

    // --- FARE CALCULATION ---
    function calculateFare() {
        let pickupCity = document.getElementById('pickupCity').value;
        let dropCity = document.getElementById('dropCity').value;
        let vehicle = document.getElementById('vehicleType').value;

        // 1. Get Base Fare for Vehicle
        let fare = BASE_RATES[vehicle];

        // 2. Check City Distance
        if (pickupCity !== dropCity) {
            // It is a cross-city delivery (Add fee)
            fare += CROSS_CITY_FEE[vehicle];
            
            // OPTIONAL: Add extra if shipping far (e.g., Santo Tomas to Calamba)
            // You can add complex if/else here if you want specific pairs
            if ( (pickupCity === 'Santo Tomas' && dropCity === 'Calamba') || 
                 (pickupCity === 'Calamba' && dropCity === 'Santo Tomas') ) {
                 fare += 50; // Extra long distance charge
            }
        }

        finalFare = fare;

        // Display Data
        document.getElementById('displayPrice').innerText = fare.toFixed(2);
        document.getElementById('displayVehicle').innerText = vehicle;
        document.getElementById('summItem').innerText = document.getElementById('itemDesc').value;
        document.getElementById('summRoute').innerText = `${pickupCity} TO ${dropCity}`;

        nextStep(3);
    }

    // --- PAYPAL ---
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{ amount: { value: finalFare } }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                // Fill Receipt
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