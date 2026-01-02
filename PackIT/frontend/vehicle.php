<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Join PackIT - Delivery Solutions</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Hover Overlay Styles -->
  <style>
    .category-card {
      position: relative;
      cursor: pointer;
    }

    .category-overlay {
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.75);
      color: #fff;
      opacity: 0;
      transition: opacity 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 1rem;
    }

    .category-card:hover .category-overlay {
      opacity: 1;
    }
  </style>
</head>

<body>

  <!-- CATEGORIES SECTION -->
  <div class="container py-5 p-lg-5">
    <div class="row">
      <div class="col mb-4 d-flex justify-content-center justify-content-lg-start pt-4">
        <div class="h1 fw-bold ps-3" style="color:#6EC064">
          Categories
        </div>
      </div>
    </div>

    <div class="row g-3 g-md-4 justify-content-center" id="categoriesContainer"></div>
  </div>

  <!-- Categories Script -->
  <script>
    const vehicles = [
      {
        vehicle: "motorcycle",
        name: "Motorcycle",
        image: "motorcycle.png",
        base_price_php: 100,
        max_weight_kg: 20,
        max_size_m: { l: 0.5, w: 0.4, h: 0.5 },
        can_carry: ["small bags", "envelopes"]
      },
      {
        vehicle: "tricycle",
        name: "Tricycle",
        image: "tricycle.png",
        base_price_php: 150,
        max_weight_kg: 50,
        max_size_m: { l: 0.7, w: 0.5, h: 0.5 },
        can_carry: ["small boxes", "bags"]
      },
      {
        vehicle: "sedan",
        name: "Sedan",
        image: "sedan.png",
        base_price_php: 200,
        max_weight_kg: 200,
        max_size_m: { l: 1.0, w: 0.6, h: 0.7 },
        can_carry: ["medium boxes", "multiple parcels"]
      },
      {
        vehicle: "pickup_truck",
        name: "Pick-up Truck",
        image: "pickup.png",
        base_price_php: 250,
        max_weight_kg: 800,
        max_size_m: { l: 2.7, w: 1.5, h: 0.5 },
        can_carry: ["big boxes", "bulky items"]
      },
      {
        vehicle: "closed_van",
        name: "Closed Van",
        image: "van.png",
        base_price_php: 300,
        max_weight_kg: 1000,
        max_size_m: { l: 2.1, w: 1.3, h: 1.3 },
        can_carry: ["pallets", "boxed goods", "perishables (depends on packaging)"]
      },
      {
        vehicle: "forward_truck",
        name: "Forward Truck",
        image: "forward-truck.png",
        base_price_php: 350,
        max_weight_kg: 1200,
        max_size_m: { l: 10.0, w: 2.4, h: 2.3 },
        can_carry: ["pallets", "bulk items", "non-perishables (depends on packaging)"]
      }
    ];

    const container = document.getElementById("categoriesContainer");

    vehicles.forEach(v => {
      const sizeText = `${v.max_size_m.l} x ${v.max_size_m.w} x ${v.max_size_m.h} m`;
      const carryText = (v.can_carry && v.can_carry.length) ? v.can_carry.join(", ") : "—";

      container.innerHTML += `
        <div class="col-6 col-lg-3">
          <div class="card h-100 rounded-4 shadow-sm overflow-hidden category-card">

            <img class="card-img-top p-3"
              src="/EASYBUY-X-PACKIT/PackIT/assets/${v.image}"
              style="height:160px; object-fit:contain;"
              alt="${v.name}">

            <div class="card-body text-center p-2" style="background-color:#DCDCDC;">
              <h6 class="fw-bold mb-0">${v.name}</h6>
            </div>

            <div class="category-overlay">
              <div>
                <div><b>Vehicle:</b> ${v.vehicle}</div>
                <div><b>Base Price:</b> ₱${v.base_price_php}</div>
                <div><b>Max Weight:</b> ${v.max_weight_kg} kg</div>
                <div><b>Max Size:</b> ${sizeText}</div>
                <div><b>Can Carry (examples):</b> ${carryText}</div>
                <div class="mt-2" style="font-size:0.9em; opacity:0.9;">
                  Final acceptance depends on actual package size & weight.
                </div>
              </div>
            </div>

          </div>
        </div>
      `;
    });
  </script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>