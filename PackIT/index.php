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
  <?php include("frontend/components/chatModal.php"); ?>
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
      <a href="#" id="chatbotLauncher" class="text-decoration-none text-dark d-flex flex-column align-items-center fw-bold small">
        <img src="assets/chatbot.png" alt="Chatbot" style="width: 48px; height: 48px; object-fit: contain;">
        <span>Chatbot</span>
      </a>
    </div>
  </div>

  <?php include("frontend/components/footer.php"); ?>

  <div class="modal fade poc-chat-modal" id="pocChatModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog shadow-lg rounded-3">
      <div class="modal-content border-0">
        
        <div class="modal-header bg-light py-2 border-bottom-0 rounded-top-3">
          <div class="d-flex align-items-center gap-2">
            <img src="assets/chatbot.png" alt="Bot" width="32" height="32" class="object-fit-contain">
            <div class="lh-1">
              <div class="fw-bold fs-6">PackIT Assistant</div>
              <small class="text-muted" style="font-size: 0.75rem;">Online</small>
            </div>
          </div>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-0">
          <div class="poc-chat-window" role="region" aria-label="Chat messages">
            <div class="poc-chat-messages" id="pocChatMessages" aria-live="polite">
              </div>

            <div class="poc-chat-input">
              <form id="pocChatForm" onsubmit="return false;">
                <div class="d-flex gap-2 align-items-center">
                  <textarea id="pocChatInput" class="form-control form-control-sm" placeholder="Type a message..." style="resize: none; height: 42px; font-size: 0.95rem;"></textarea>
                  <button id="pocSendBtn" class="btn btn-primary btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" type="button">
                    <i class="bi bi-send-fill"></i>
                  </button>
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

      // --- INTEGRATED CHAT LOGIC ---
      // This part now uses the code from chat.php, but listens to 'chatbotLauncher'
      const launcher = document.getElementById('chatbotLauncher');
      const modalEl = document.getElementById('pocChatModal');
      const messagesEl = document.getElementById('pocChatMessages');
      const inputEl = document.getElementById('pocChatInput');
      const sendBtn = document.getElementById('pocSendBtn');
      
      // Update this path to where your chatai.php actually lives
      const chatEndpoint = "frontend/chatai.php"; 

      let modal;

      function appendMessage(text, who = 'bot') {
        const wrapper = document.createElement('div');
        wrapper.className = `poc-chat-msg ${who}`;
        
        const avatar = `<div class="avatar">${who === 'user' ? 'U' : 'AI'}</div>`;
        const bubble = `<div class="poc-chat-bubble">${text.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[m]))}</div>`;

        wrapper.innerHTML = who === 'user' ? (bubble + avatar) : (avatar + bubble);
        messagesEl.appendChild(wrapper);
        messagesEl.scrollTop = messagesEl.scrollHeight;
      }

      function appendTypingIndicator() {
        const wrap = document.createElement('div');
        wrap.className = 'poc-chat-msg bot';
        wrap.innerHTML = `<div class="avatar">AI</div><div class="poc-chat-bubble"><div class="poc-chat-typing"></div></div>`;
        messagesEl.appendChild(wrap);
        messagesEl.scrollTop = messagesEl.scrollHeight;
        return wrap;
      }

      async function sendMessage(text) {
        if (!text || !text.trim()) return;
        const message = text.trim();

        appendMessage(message, 'user');
        inputEl.value = '';
        inputEl.disabled = true;
        sendBtn.disabled = true;

        const typingNode = appendTypingIndicator();

        try {
          const fd = new FormData();
          fd.append('prompt', message);

          const res = await fetch(chatEndpoint, { method: 'POST', body: fd });
          const textResp = await res.text();

          typingNode.remove();

          if (!res.ok) appendMessage('System error. Please try again.', 'bot');
          else appendMessage(textResp, 'bot');
          
        } catch (err) {
          typingNode.remove();
          appendMessage('Network error. Check connection.', 'bot');
        } finally {
          inputEl.disabled = false;
          sendBtn.disabled = false;
          inputEl.focus();
        }
      }

      // Event Listeners for Chat
      if (launcher) {
        launcher.addEventListener('click', function(ev) {
          ev.preventDefault();
          if (!modal) modal = new bootstrap.Modal(modalEl);
          modal.show();
        });
      }

      if (sendBtn) {
        sendBtn.addEventListener('click', () => sendMessage(inputEl.value));
        inputEl.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(inputEl.value); }
        });
      }

      modalEl.addEventListener('shown.bs.modal', function() {
        inputEl.focus();
        if (messagesEl.children.length === 0) {
          appendMessage('Hi! I am Pack IT Assistant. How can I help you today?', 'bot');
        }
      });

    })();
  </script>
</body>
</html>