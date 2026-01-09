<?php session_start(); ?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pack IT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
  <style>
    /* Hide the floating chat bubble on the homepage since we have the menu launcher */
    .poc-chat-btn {
      display: none !important;
    }
  </style>
</head>

<body id="top" class="min-vh-100 d-flex flex-column" style="font-family: 'Segoe UI', sans-serif; background: linear-gradient(180deg, #ffffff 0%, #fcfcfd 100%); overflow-x: hidden;">

  <?php $page = basename($_SERVER['PHP_SELF']); ?>
  <?php include("frontend/components/navbar.php"); ?>

  <main class="container-fluid my-1 py-lg-2 flex-grow-1">
    <div class="row align-items-center gy-5">
      <div class="col-12 col-lg-6 d-flex justify-content-center justify-content-lg-start position-relative z-1">
        <img src="assets/duck.png" class="img-fluid" alt="Duck Mascot" style="max-width:520px; transition: transform .25s ease;">
      </div>
      <div class="col-12 col-lg-6 d-flex align-items-center text-center text-lg-start ms-lg-n5 z-2">
        <div class="mx-auto mx-lg-0" style="max-width:44ch;">
          <h1 class="display-1 fw-bold text-uppercase mb-3" style="letter-spacing: -0.02em; line-height: 0.95; color: #111;">PACK IT</h1>
          <p class="lead mb-3" style="color: #555;">
            The gold standard of PH logistics.🏆<br>
            Bridging gaps and breaking records, one delivery at a time.
          </p>
        </div>
      </div>
    </div>
    
    <?php include("frontend/components/aboutUs.php"); ?>
  </main>

  <div id="floatingActions" 
       class="position-fixed d-flex flex-column align-items-center rounded-5 shadow z-3" 
       style="background: rgba(248, 225, 91, 0.98); top: 50%; right: 20px; transform: translateY(-50%); transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1), max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), padding 0.4s ease; width: 110px; max-height: 500px; padding: 1rem; overflow: hidden;">
    
    <button id="actionsToggleBtn" 
            class="btn border-0 p-0 d-flex d-lg-none align-items-center justify-content-center rounded-circle" 
            style="width: 56px; height: 56px; background: transparent; color: #111; flex-shrink: 0;"
            aria-label="Toggle Menu">
      <i class="bi bi-list fs-4" id="toggleIcon"></i>
    </button>

    <div id="actionLinks" class="d-flex flex-column align-items-center gap-3 w-100" style="transition: opacity 0.3s ease; white-space: nowrap;">
      
      <div class="d-lg-none" style="height: 5px;"></div>

      <a href="../PackIT/frontend/booking/package.php" class="text-decoration-none text-dark d-flex flex-column align-items-center fw-bold small">
        <img src="assets/box.png" alt="book" style="width: 48px; height: 48px; object-fit: contain;">
        <span>Book</span>
      </a>
      <a href="../PackIT/frontend/tracking.php" class="text-decoration-none text-dark d-flex flex-column align-items-center fw-bold small">
        <img src="assets/tracking.png" alt="tracking" style="width: 48px; height: 48px; object-fit: contain;">
        <span>Tracking</span>
      </a>
      <a href="#" id="chatbotLauncher" class="text-decoration-none text-dark d-flex flex-column align-items-center fw-bold small">
        <img src="assets/chatbot.png" alt="Chatbot" style="width: 48px; height: 48px; object-fit: contain;">
        <span>Chatbot</span>
      </a>
    </div>
  </div>

  <?php include("frontend/components/footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <?php include("frontend/components/chatModal.php"); ?>

  <script>
    (function() {
      // --- FLOATING ACTION MENU LOGIC ---
      const floatingActions = document.getElementById('floatingActions');
      const toggleBtn = document.getElementById('actionsToggleBtn');
      const toggleIcon = document.getElementById('toggleIcon');
      const actionLinks = document.getElementById('actionLinks');
      let isExpanded = false;

      function setMenuState(open) {
        if (open) {
          floatingActions.style.width = '110px';
          floatingActions.style.maxHeight = '500px'; 
          floatingActions.style.padding = '1rem';
          floatingActions.style.borderRadius = 'var(--bs-border-radius-xl)';
          actionLinks.style.opacity = '1';
          toggleIcon.classList.remove('bi-list');
          toggleIcon.classList.add('bi-x-lg');
          isExpanded = true;
        } else {
          floatingActions.style.width = '56px'; 
          floatingActions.style.maxHeight = '56px'; 
          floatingActions.style.padding = '0';
          floatingActions.style.borderRadius = '50px';
          actionLinks.style.opacity = '0';
          toggleIcon.classList.remove('bi-x-lg');
          toggleIcon.classList.add('bi-list');
          isExpanded = false;
        }
      }

      toggleBtn.addEventListener('click', () => setMenuState(!isExpanded));

      function checkScreen() {
        if (window.innerWidth >= 992) {
          floatingActions.style.width = '110px';
          floatingActions.style.maxHeight = '500px'; 
          floatingActions.style.padding = '1rem';
          floatingActions.style.borderRadius = 'var(--bs-border-radius-xl)';
          actionLinks.style.opacity = '1';
        } else {
          if (!floatingActions.hasAttribute('data-init')) {
             setMenuState(false);
             floatingActions.setAttribute('data-init', 'true');
          }
        }
      }
      window.addEventListener('resize', checkScreen);
      checkScreen();

      // Note: Logic for chatbotLauncher click is handled automatically by chat.php
    })();
  </script>
</body>
</html>