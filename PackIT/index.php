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
       style="background: rgba(248, 225, 91, 0.98); top: 50%; right: 20px; transform: translateY(-50%); transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1), max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), padding 0.4s ease; overflow: hidden; z-index: 1050;">
    
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
      <a href="../PackIT/frontend/chatai.php" id="chatbotLauncher" class="text-decoration-none text-dark d-flex flex-column align-items-center fw-bold small">
        <img src="assets/chatbot.png" alt="Chatbot" style="width: 48px; height: 48px; object-fit: contain;">
        <span>Chatbot</span>
      </a>
    </div>
  </div>

  <?php include("frontend/components/footer.php"); ?>

  <div class="modal fade" id="chatModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog shadow-lg rounded-4" style="position: fixed; right: 20px; bottom: 90px; margin: 0; z-index: 1060; max-width: 420px;">
      <div class="modal-content border-0">
        <div class="modal-header border-bottom-0 bg-light rounded-top-4">
          <div class="d-flex align-items-center gap-2">
            <img src="assets/chatbot.png" alt="bot" style="width: 36px; height: 36px;">
            <div>
              <div class="fw-bold">Pack IT Assistant</div>
              <div class="small text-secondary">Ask about bookings, tracking, and packaging</div>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <div class="d-flex flex-column" style="height: 520px;">
            <div id="chatMessages" class="flex-grow-1 p-3 overflow-auto d-flex flex-column gap-3" style="background: linear-gradient(180deg, #f8f9fa, #fff);"></div>
            <div class="p-2 bg-white border-top">
              <form id="chatForm" onsubmit="return false;">
                <div class="d-flex gap-2 align-items-center">
                  <textarea id="chatInput" class="form-control" placeholder="Type your message..." style="height: 46px; resize: none;"></textarea>
                  <button id="sendBtn" class="btn btn-warning" type="button"><i class="bi bi-send"></i></button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    (function() {
      // --- FLOATING ACTION SLIDE LOGIC ---
      const floatingActions = document.getElementById('floatingActions');
      const toggleBtn = document.getElementById('actionsToggleBtn');
      const toggleIcon = document.getElementById('toggleIcon');
      const actionLinks = document.getElementById('actionLinks');

      let isExpanded = false;

      function setMenuState(open) {
        if (open) {
          // --- OPEN (SLIDE OUT TO LEFT) ---
          // Width: 110px
          // Max-Height: 500px (allows full vertical expansion)
          floatingActions.style.width = '110px';
          floatingActions.style.maxHeight = '500px'; 
          floatingActions.style.padding = '1rem';
          floatingActions.style.borderRadius = 'var(--bs-border-radius-xl)';
          
          actionLinks.style.opacity = '1';
          
          toggleIcon.classList.remove('bi-list');
          toggleIcon.classList.add('bi-x-lg');
          isExpanded = true;
        } else {
          // --- CLOSED (SLIDE IN TO RIGHT) ---
          // Width: 56px (Same as button)
          // Max-Height: 56px (Same as button)
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

      toggleBtn.addEventListener('click', () => {
        setMenuState(!isExpanded);
      });

      // Responsive Check: Desktop always open, Mobile starts closed
      function checkScreen() {
        if (window.innerWidth >= 992) {
          // Desktop: Force Open
          floatingActions.style.width = '110px';
          floatingActions.style.maxHeight = '500px'; 
          floatingActions.style.padding = '1rem';
          floatingActions.style.borderRadius = 'var(--bs-border-radius-xl)';
          actionLinks.style.opacity = '1';
        } else {
          // Mobile: Start Closed (Only on first load)
          if (!floatingActions.hasAttribute('data-init')) {
             setMenuState(false);
             floatingActions.setAttribute('data-init', 'true');
          }
        }
      }

      window.addEventListener('resize', checkScreen);
      checkScreen();

      // --- CHAT LOGIC ---
      const launcher = document.getElementById('chatbotLauncher');
      const chatModalEl = document.getElementById('chatModal');
      const chatMessages = document.getElementById('chatMessages');
      const chatInput = document.getElementById('chatInput');
      const sendBtn = document.getElementById('sendBtn');
      const chatEndpoint = launcher ? launcher.getAttribute('href') : 'frontend/chatai.php';
      let modal;

      function appendMessage(text, who = 'bot') {
        const wrapper = document.createElement('div');
        wrapper.className = `d-flex gap-2 align-items-end ${who === 'user' ? 'justify-content-end' : ''}`;
        const avatar = `<div class="d-flex align-items-center justify-content-center fw-bold bg-light rounded-circle border" style="width:36px;height:36px;flex:0 0 36px;">${who === 'user' ? 'U' : 'AI'}</div>`;
        const bubbleStyle = who === 'user' ? 'background-color: #0d6efd; color: white;' : 'background-color: #f1f3ff; color: #111;';
        const bubble = `<div class="shadow-sm p-2 rounded-3" style="max-width: 78%; ${bubbleStyle}">${text.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[m]))}</div>`;
        wrapper.innerHTML = who === 'user' ? (bubble + avatar) : (avatar + bubble);
        chatMessages.appendChild(wrapper);
        chatMessages.scrollTop = chatMessages.scrollHeight;
      }

      function appendTypingIndicator() {
        const wrap = document.createElement('div');
        wrap.className = 'd-flex gap-2 align-items-end';
        wrap.innerHTML = `<div class="d-flex align-items-center justify-content-center fw-bold bg-light rounded-circle border" style="width:36px;height:36px;">AI</div><div class="shadow-sm p-2 rounded-3 bg-light text-muted">typing...</div>`;
        chatMessages.appendChild(wrap);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return wrap;
      }

      if (launcher) {
        launcher.addEventListener('click', function(ev) {
          ev.preventDefault();
          if (!modal) modal = new bootstrap.Modal(chatModalEl);
          modal.show();
        });
      }

      chatModalEl.addEventListener('shown.bs.modal', function() {
        chatInput.focus();
        if (chatMessages.children.length === 0) appendMessage('Hi! I am Pack IT Assistant. How can I help you today?', 'bot');
      });

      if (sendBtn) {
        sendBtn.addEventListener('click', async () => {
          const txt = chatInput.value.trim();
          if(!txt) return;
          appendMessage(txt, 'user');
          chatInput.value = '';
          const typing = appendTypingIndicator();
          try {
              const fd = new FormData(); fd.append('prompt', txt);
              const res = await fetch(chatEndpoint, { method: 'POST', body: fd });
              const respTxt = await res.text();
              typing.remove();
              appendMessage(respTxt || 'Error', 'bot');
          } catch(e) {
              typing.remove();
              appendMessage('Network error', 'bot');
          }
        });
        
         chatInput.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendBtn.click(); }
        });
      }
    })();
  </script>
</body>
</html>