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
        var categories = [
  {
    name: "Motorcycle",
    image: "motorcycle.png",
    description: `
      Package Type: Envelope / Bag<br>
      Base Price: ₱100<br>
      Max Weight: 20 kg<br>
      Size: 0.5 x 0.4 x 0.5 m
    `
  },
  {
    name: "Tricycle",
    image: "tricycle.png",
    description: `
      Package Type: Small Box<br>
      Base Price: ₱150<br>
      Max Weight: 50 kg<br>
      Size: 0.7 x 0.5 x 0.5 m
    `
  },
  {
    name: "Sedan",
    image: "sedan.png",
    description: `
      Package Type: Medium Box<br>
      Base Price: ₱200<br>
      Max Weight: 200 kg<br>
      Size: 1.0 x 0.6 x 0.7 m
    `
  },
  {
    name: "Pick-up Truck",
    image: "pickup.png",
    description: `
      Package Type: Big Box<br>
      Base Price: ₱250<br>
      Max Weight: 800 kg<br>
      Size: 2.7 x 1.5 x 0.5 m
    `
  },
  {
    name: "Closed Van",
    image: "van.png",
    description: `
      Package Type: Pallet (Perishable)<br>
      Base Price: ₱300<br>
      Max Weight: 1,000 kg<br>
      Size: 2.1 x 1.3 x 1.3 m
    `
  },
  {
    name: "Forward Truck",
    image: "forward-truck.png",
    description: `
      Package Type: Pallet (Non-Perishable)<br>
      Base Price: ₱350<br>
      Max Weight: 1,200 kg<br>
      Size: 10.0 x 2.4 x 2.3 m
    `
  }
];

        var container = document.getElementById("categoriesContainer");

        categories.forEach(cat => {
            container.innerHTML += `
        <div class="col-6 col-lg-3">
          <div class="card h-100 rounded-4 shadow-sm overflow-hidden category-card">

          <img class="card-img-top p-3"
     src="/EASYBUY-X-PACKIT/PackIT/assets/${cat.image}"
     style="height:160px; object-fit:contain;"
     alt="${cat.name}">


            <div class="card-body text-center p-2" style="background-color:#DCDCDC;">
              <h6 class="fw-bold mb-0">${cat.name}</h6>
            </div>

            <div class="category-overlay">
              <div>${cat.description}</div>
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