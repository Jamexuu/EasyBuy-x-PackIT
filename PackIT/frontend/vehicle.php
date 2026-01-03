<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Join PackIT - Delivery Solutions</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    #categoriesContainer {
      overflow-x: auto;
      overflow-y: hidden;
      scrollbar-width: none;
      -ms-overflow-style: none;
      scroll-behavior: smooth;
    }

    #categoriesContainer::-webkit-scrollbar {
      display: none;
    }

    .vehicle-wrapper {
      flex: 0 0 auto;
      width: 85vw;
    }

    @media (min-width: 768px) {
      .vehicle-wrapper {
        flex: 0 0 auto;
        width: min(85vw, 300px);
      }
    }

    .vehicle-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border: 2px solid transparent;
      display: flex;
      flex-direction: column;
    }

    .vehicle-card:hover {
      transform: translateY(-5px);
      border-color: #f8e14b;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .vehicle-card img {
      max-height: 160px;
      width: 100%;
      height: auto;
      object-fit: contain;
      max-width: 100%;
      height: 160px;
    }

    .scroll-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      z-index: 10;
      width: clamp(36px, 4vw, 45px);
      height: clamp(36px, 4vw, 45px);
      border-radius: 50%;
      background-color: #fff;
      border: 1px solid #eee;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .scroll-btn:hover {
      background-color: #f8e14b;
      border-color: #f8e14b;
      color: #000;
    }

    .scroll-btn:active {
      transform: translateY(-50%) scale(0.95);
    }

    .scroll-btn-left {
      left: clamp(4px, 1vw, 16px);
    }

    .scroll-btn-right {
      right: clamp(4px, 1vw, 16px);
    }

    @media (max-width: 768px) {
      .scroll-btn {
        display: none;
      }

      .scroll-container-wrapper {
        padding: 0 !important;
      }
    }

    @media (max-width: 576px) {
      .vehicle-card img {
        height: 120px;
      }
    }
  </style>
</head>

<body>

  <div class="container-fluid py-5 p-lg-5">
    <div class="row">
      <div class="col mb-4 pt-4">
        <h1 class="fw-bold ps-2 border-start border-5 border-warning text-dark">
          Vehicle Type
        </h1>
      </div>
    </div>

    <div class="position-relative scroll-container-wrapper">
      <button class="scroll-btn scroll-btn-left" onclick="scrollContainer('left')" aria-label="Scroll Left">
        <i class="bi bi-chevron-left fs-5"></i>
      </button>

      <div class="d-flex flex-nowrap py-3 gap-3" id="categoriesContainer"></div>

      <button class="scroll-btn scroll-btn-right" onclick="scrollContainer('right')" aria-label="Scroll Right">
        <i class="bi bi-chevron-right fs-5"></i>
      </button>
    </div>
  </div>

  <script>
    var categories = [{
        name: "Motorcycle",
        image: "motorcycle.png",
        description: `<small class="text-muted">Type:</small> <strong>Envelope</strong><br><small class="text-muted">Price:</small> <strong>₱100</strong><br><small class="text-muted">Max:</small> <strong>20 kg</strong><br><small class="text-muted">Size:</small> <strong>0.5 x 0.4 x 0.5 Meter</strong>`
      },
      {
        name: "Tricycle",
        image: "tricycle.png",
        description: `<small class="text-muted">Type:</small> <strong>Small Box</strong><br><small class="text-muted">Price:</small> <strong>₱150</strong><br><small class="text-muted">Max:</small> <strong>50 kg</strong><br><small class="text-muted">Size:</small> <strong>0.7 x 0.5 x 0.5 Meter</strong>`
      },
      {
        name: "Sedan",
        image: "sedan.png",
        description: `<small class="text-muted">Type:</small> <strong>Med Box</strong><br><small class="text-muted">Price:</small> <strong>₱200</strong><br><small class="text-muted">Max:</small> <strong>200 kg</strong><br><small class="text-muted">Size:</small> <strong>1.0 x 0.6 x 0.7 Meter</strong>`
      },
      {
        name: "Pick-up Truck",
        image: "pickup.png",
        description: `<small class="text-muted">Type:</small> <strong>Big Box</strong><br><small class="text-muted">Price:</small> <strong>₱250</strong><br><small class="text-muted">Max:</small> <strong>800 kg</strong><br><small class="text-muted">Size:</small> <strong>2.7 x 1.5 x 0.5 Meter</strong>`
      },
      {
        name: "Closed Van",
        image: "van.png",
        description: `<small class="text-muted">Type:</small> <strong>Pallet (Perishable)</strong><br><small class="text-muted">Price:</small> <strong>₱300</strong><br><small class="text-muted">Max:</small> <strong>1000 kg</strong><br><small class="text-muted">Size:</small> <strong>2.1 x 1.3 x 1.3 Meter</strong>`
      },
      {
        name: "Forward Truck",
        image: "forward truck.png",
        description: `<small class="text-muted">Type:</small> <strong>Pallet (Non Perishable)</strong><br><small class="text-muted">Price:</small> <strong>₱350</strong><br><small class="text-muted">Max:</small> <strong>1200 kg</strong><br><small class="text-muted">Size:</small> <strong>10.0 x 2.4 x 2.3 Meter</strong>`
      }
    ];

    var container = document.getElementById("categoriesContainer");
    var contentHTML = "";

    categories.forEach(cat => {
      contentHTML += `
        <div class="vehicle-wrapper">
          <div class="card rounded-4 shadow-sm vehicle-card">
            <img class="card-img-top p-3 img-fluid"
                 src="/EASYBUY-X-PACKIT/PackIT/assets/${cat.image}"
                 style="height:160px; object-fit:contain;"
                 alt="${cat.name}">
            <div class="card-body border-top d-flex flex-column">
              <h5 class="fw-bold mb-3 text-center" style="color:#333">${cat.name}</h5>
              <div class="p-3 rounded-3 mt-auto" style="background-color:#f8f9fa;">
                <p class="card-text small mb-0" style="line-height: 1.6;">
                  ${cat.description}
                </p>
              </div>
            </div>
          </div>
        </div>
      `;
    });

    container.innerHTML = contentHTML;

    function scrollContainer(direction) {
      const firstCard = container.querySelector('.vehicle-wrapper');
      if (!firstCard) return;

      const style = window.getComputedStyle(container);
      const gap = parseInt(style.gap) || 0;
      const scrollAmount = firstCard.offsetWidth + gap;

      container.scrollBy({
        left: direction === 'left' ? -scrollAmount : scrollAmount,
        behavior: 'smooth'
      });
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>