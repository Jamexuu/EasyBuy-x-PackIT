<?php
session_start();

require_once '../api/classes/Database.php';

$db = new Database();

// Fetch vehicles from DB
$stmt = $db->executeQuery("SELECT * FROM vehicles ORDER BY id ASC");
$vehicles = $db->fetch($stmt);

function peso($amount) {
  return '₱' . number_format((float)$amount, 0);
}

function meters3($l, $w, $h) {
  $fmt = fn($x) => rtrim(rtrim(number_format((float)$x, 1), '0'), '.');
  return $fmt($l) . ' x ' . $fmt($w) . ' x ' . $fmt($h) . ' Meter';
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Join PackIT - Delivery Solutions</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    /* Custom Hover Effects (Cannot be done purely with Bootstrap utilities) */
    .vehicle-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
      border: 2px solid transparent; /* Invisible border to prevent layout shift */
    }
    .vehicle-card:hover {
      transform: translateY(-8px); /* Lift up effect */
      box-shadow: 0 1rem 3rem rgba(0,0,0,.15)!important;
      border-color: #f8e14b !important; /* Brand Yellow Border */
    }
    
    /* Hide scrollbar but keep functionality */
    #categoriesContainer {
      scrollbar-width: none; /* Firefox */
      -ms-overflow-style: none;  /* IE 10+ */
    }
    #categoriesContainer::-webkit-scrollbar { 
      display: none; /* Chrome/Safari */
    }
  </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-light font-sans-serif">

  <?php include 'components/navbar.php'; ?>

  <main class="container-fluid py-5 px-lg-5 flex-grow-1">
    
    <div class="row mb-4 pt-4">
      <div class="col">
        <div class="ps-3 border-start border-5 border-warning">
          <h1 class="fw-bold text-dark m-0">Vehicle Type</h1>
        </div>
      </div>
    </div>

    <div class="position-relative">
      
      <button class="btn btn-light rounded-circle shadow position-absolute top-50 start-0 translate-middle-y z-2 d-flex align-items-center justify-content-center border-0" 
              style="width: 48px; height: 48px; margin-left: -10px;" 
              onclick="scrollContainer('left')" 
              aria-label="Scroll Left">
        <i class="bi bi-chevron-left fs-5"></i>
      </button>

      <div id="categoriesContainer" 
           class="d-flex flex-nowrap gap-4 overflow-x-auto px-2 pb-5 pt-3" 
           style="scroll-behavior: smooth;">
        
        <?php foreach ($vehicles as $v): ?>
          <?php
            $name = $v['name'] ?? '';
            $img  = $v['image_file'] ?? '';
            // Pre-calculate data
            $type = htmlspecialchars($v['package_type'] ?? '');
            $fare = peso($v['fare'] ?? 0);
            $max  = htmlspecialchars((string)($v['max_kg'] ?? 0));
            $size = htmlspecialchars(meters3($v['size_length_m'] ?? 0, $v['size_width_m'] ?? 0, $v['size_height_m'] ?? 0));
          ?>
          
          <div class="flex-shrink-0" style="width: 85vw; max-width: 320px;">
            <div class="card h-100 shadow-sm rounded-4 vehicle-card">
              
              <div class="p-4 d-flex align-items-center justify-content-center bg-white rounded-top-4" style="height: 200px;">
                 <img class="img-fluid object-fit-contain" 
                      src="/EASYBUY-X-PACKIT/PackIT/assets/<?= htmlspecialchars($img) ?>" 
                      alt="<?= htmlspecialchars($name) ?>"
                      style="max-height: 100%;">
              </div>

              <div class="card-body d-flex flex-column border-top bg-white rounded-bottom-4">
                <h5 class="fw-bold text-center mb-3 text-dark"><?= htmlspecialchars($name) ?></h5>
                
                <div class="p-3 rounded-3 mt-auto bg-light">
                  <div class="small lh-lg">
                    <div class="d-flex justify-content-between">
                      <span class="text-secondary">Type:</span>
                      <span class="fw-semibold text-dark"><?= $type ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                      <span class="text-secondary">Price:</span>
                      <span class="fw-semibold text-dark"><?= $fare ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                      <span class="text-secondary">Max:</span>
                      <span class="fw-semibold text-dark"><?= $max ?> kg</span>
                    </div>
                    <div class="d-flex justify-content-between">
                      <span class="text-secondary">Size:</span>
                      <span class="fw-semibold text-dark text-end"><?= $size ?></span>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <button class="btn btn-light rounded-circle shadow position-absolute top-50 end-0 translate-middle-y z-2 d-flex align-items-center justify-content-center border-0" 
              style="width: 48px; height: 48px; margin-right: -10px;" 
              onclick="scrollContainer('right')" 
              aria-label="Scroll Right">
        <i class="bi bi-chevron-right fs-5"></i>
      </button>

    </div>
  </main>

  <?php include("../frontend/components/chat.php"); ?>
  <?php include 'components/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const container = document.getElementById("categoriesContainer");

    function scrollContainer(direction) {
      // Calculate width of card + gap
      const card = container.querySelector('.flex-shrink-0');
      if (!card) return;
      
      const scrollAmount = card.offsetWidth + 24; // Width + gap (1.5rem = 24px)

      container.scrollBy({
        left: direction === 'left' ? -scrollAmount : scrollAmount,
        behavior: 'smooth'
      });
    }
  </script>
</body>
</html>