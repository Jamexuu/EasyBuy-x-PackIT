<?php session_start(); ?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pack IT</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root {
      --brand-yellow: #f8e15b;
      --brand-dark: #111;
      --brand-gray: #555;
      --soft-shadow: 0 8px 28px rgba(16, 24, 40, 0.06);
      --glass: rgba(255, 255, 255, 0.6);
    }

    /* Base */
    html,
    body {
      height: 100%;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      overflow-x: hidden;
      background: linear-gradient(180deg, #ffffff 0%, #fcfcfd 100%);
      color: var(--brand-dark);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    /* Container spacing */
    main.container {
      padding-top: 3.5rem;
      padding-bottom: 3.5rem;
    }

    /* HERO area */
    .display-1 {
      font-weight: 900;
      letter-spacing: -0.02em;
      line-height: 0.95;
      color: var(--brand-dark);
    }

    .lead {
      color: var(--brand-gray);
      font-size: 1.05rem;
      max-width: 56ch;
    }

    /* Get Started button */
    .btn.bg-brand {
      background: linear-gradient(90deg, var(--brand-yellow), #ffe27a);
      color: var(--brand-dark);
      border: none;
      box-shadow: var(--soft-shadow);
      transition: transform .16s ease, box-shadow .16s ease;
    }

    .btn.bg-brand:hover,
    .btn.bg-brand:focus {
      transform: translateY(-3px);
      box-shadow: 0 14px 40px rgba(16, 24, 40, 0.10);
      color: var(--brand-dark);
    }

    /* Mascot image */
    .hero-img {
      max-height: 450px;
      width: auto;
      transition: transform .25s ease;
      border-radius: 12px;
      box-shadow: var(--soft-shadow);
    }

    .hero-img:hover {
      transform: translateY(-6px) scale(1.02);
    }

    /* ---------- Bigger floating actions (replace your existing floating-actions rules) ---------- */

    /* container: taller/wider pill on the right edge */
    .floating-actions {
      position: fixed;
      top: 50%;
      right: 20px;
      transform: translateY(-50%);
      width: 110px;
      /* wider */
      padding: 18px 12px;
      /* more padding */
      border-radius: 28px;
      /* pill shape */
      background: rgba(248, 225, 91, 0.98);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 18px;
      /* more space between items */
      box-shadow: 0 14px 40px rgba(16, 24, 40, 0.10);
      z-index: 1050;
      transition: all .28s ease;
      overflow: visible;
    }

    /* toggle button (top small square inside pill) */
    .floating-actions .action-toggle-btn {
      width: 56px;
      height: 56px;
      padding: 0;
      border-radius: 10px;
      border: none;
      background: rgba(248, 225, 91, 0.98);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 1.15rem;
      color: var(--brand-dark, #111);
      /* ensure toggle stays visually at top of pill */
      margin-top: -6px;
    }

    /* links vertically stacked inside the pill */
    .action-links-wrapper {
      display: flex;
      flex-direction: column;
      gap: 18px;
      align-items: center;
      padding: 0;
      margin: 0;
    }

    /* each action: larger icon + label */
    .floating-actions a {
      display: inline-flex;
      flex-direction: column;
      align-items: center;
      text-decoration: none;
      color: var(--brand-dark);
      font-size: 0.95rem;
      /* slightly larger label */
      font-weight: 700;
      gap: 8px;
      transition: transform .14s ease, filter .14s ease;
    }

    /* bigger images */
    .floating-actions a img {
      width: 56px;
      /* increased icon size */
      height: 56px;
      object-fit: contain;
      filter: drop-shadow(0 8px 20px rgba(0, 0, 0, 0.08));
    }

    /* hover lift */
    .floating-actions a:hover {
      transform: translateY(-6px);
      filter: brightness(1.02);
    }

    /* collapsed state: small circular toggle only */
    .floating-actions.closed {
      width: 72px;
      padding: 8px;
      border-radius: 50%;
      right: 20px;
    }

    /* hide action list when collapsed */
    .floating-actions.closed .action-links-wrapper {
      display: none;
    }

    /* adjust toggle to fill circular collapsed container */
    .floating-actions.closed .action-toggle-btn {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      margin-top: 0;
    }

    /* Responsive: keep it slightly smaller on narrow screens */
    @media (max-width: 991px) {
      .floating-actions {
        width: 92px;
        padding: 12px;
        right: 12px;
        border-radius: 22px;
        gap: 12px;
      }

      .floating-actions .action-toggle-btn {
        width: 48px;
        height: 48px;
        font-size: 1rem;
      }

      .floating-actions a img {
        width: 48px;
        height: 48px;
      }

      .floating-actions a {
        font-size: 0.9rem;
      }
    }

    /* Chat modal/widget styling kept but refined */
    .chat-widget .modal-dialog {
      max-width: 420px;
      margin: 0;
      position: fixed;
      right: 20px;
      bottom: 90px;
      transform: translateZ(0);
      z-index: 1060;
    }

    .chat-window {
      height: 520px;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      border-radius: 8px;
    }

    .chat-messages {
      flex: 1 1 auto;
      padding: 12px;
      overflow-y: auto;
      background: linear-gradient(180deg, #f8f9fa, #fff);
    }

    .chat-input {
      border-top: 1px solid #e9ecef;
      padding: 10px;
      background: #fff;
    }

    .chat-msg {
      display: flex;
      gap: 8px;
      margin-bottom: 10px;
      align-items: flex-end;
    }

    .chat-msg .avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      overflow: hidden;
      flex: 0 0 36px;
      background: #e9ecef;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      color: #333;
    }

    .chat-bubble {
      max-width: 78%;
      padding: 9px 12px;
      border-radius: 12px;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
      line-height: 1.3;
      white-space: pre-wrap;
      background: #f1f3ff;
    }

    .chat-msg.user {
      justify-content: flex-end;
    }

    .chat-msg.user .chat-bubble {
      background: #0d6efd;
      color: #fff;
      border-bottom-right-radius: 4px;
    }

    .chat-msg.bot .chat-bubble {
      background: #f1f3ff;
      color: #111;
      border-bottom-left-radius: 4px;
    }

    .chat-typing {
      display: inline-block;
      width: 36px;
      height: 12px;
      background: linear-gradient(90deg, #e1e4ff, #c6d0ff);
      border-radius: 6px;
      animation: blink 1.2s infinite;
    }

    @keyframes blink {
      0% {
        opacity: .25;
        transform: translateY(0);
      }

      50% {
        opacity: 1;
        transform: translateY(-2px);
      }

      100% {
        opacity: .25;
        transform: translateY(0);
      }
    }

    .chat-controls {
      display: flex;
      gap: 8px;
      align-items: center;
    }

    .chat-controls textarea {
      resize: none;
      height: 46px;
    }

    /* Footer curve (kept) */
    .footer-curve {
      height: 90px;
      background: var(--brand-yellow);
      clip-path: ellipse(85% 100% at 50% 100%);
    }

    /* small reveal */
    .reveal {
      opacity: 0;
      transform: translateY(10px);
      transition: opacity .45s ease, transform .45s ease;
    }

    .reveal.visible {
      opacity: 1;
      transform: none;
    }

    @media (min-width: 992px) {
      .near-duck {
        margin-left: -120px;
        /* move closer on lg */
      }
    }

    @media (min-width: 1200px) {
      .near-duck {
        margin-left: -200px;
        /* move even more on xl */
      }
    }
  </style>
</head>

<body id="top" class="min-vh-100">

  <?php $page = basename($_SERVER['PHP_SELF']); ?>
  <?php include("frontend/components/navbar.php"); ?>

  <main class="container-fluid my-1 py-lg-2">
    <div class="row align-items-center gy-5">
      <!-- LEFT: Duck image -->
      <div class="col-12 col-lg-6 d-flex justify-content-center justify-content-lg-start">
        <img src="assets/duck.png" class="img-fluid" alt="Duck Mascot" style="max-width:520px; height:auto; transition: transform .25s ease;">
      </div>

      <!-- RIGHT: PACK IT (beside the duck on lg+, stacked on sm) -->
      <div class="col-12 col-lg-6 d-flex align-items-center text-center text-lg-start near-duck">
        <div style="max-width:44ch;">
          <h1 class="display-1 fw-bold text-uppercase mb-3">PACK IT</h1>

          <p class="lead mb-3" style="color: var(--brand-gray);">
            The gold standard of PH logistics.🏆<br>
            Bridging gaps and breaking records, one delivery at a time.
          </p>
        </div>
      </div>
    </div>
    <?php include("frontend/components/aboutUs.php"); ?>

  </main>

  <!-- Floating Action Menu -->
  <div class="floating-actions" id="floatingActions" aria-expanded="true">
    <button class="action-toggle-btn" id="actionsToggleBtn" aria-label="Toggle Menu">
      <i class="bi bi-x-lg" id="toggleIcon"></i>
    </button>

    <div class="action-links-wrapper">
      <a href="../PackIT/frontend/booking/package.php">
        <img src="assets/box.png" alt="book">
        <span>Book</span>
      </a>
      <a href="../PackIT/frontend/tracking.php">
        <img src="assets/tracking.png" alt="tracking">
        <span>Tracking</span>
      </a>
      <a href="../PackIT/frontend/chatai.php" id="chatbotLauncher">
        <img src="assets/chatbot.png" alt="Chatbot">
        <span>Chatbot</span>
      </a>
    </div>
  </div>

  <?php include("frontend/components/footer.php"); ?>

  <div class="modal fade chat-widget" id="chatModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <div class="d-flex align-items-center gap-2">
            <img src="assets/chatbot.png" alt="bot" style="width: 36px;height:36px;">
            <div>
              <div class="fw-bold">Pack IT Assistant</div>
              <div class="muted-sm" style="font-size: 0.85rem;color:#6c757d">Ask about bookings, tracking, and packaging</div>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-0">
          <div class="chat-window">
            <div class="chat-messages" id="chatMessages" aria-live="polite" aria-atomic="false"></div>

            <div class="chat-input">
              <form id="chatForm" onsubmit="return false;">
                <div class="chat-controls">
                  <textarea id="chatInput" class="form-control" placeholder="Type your message and press Enter to send" aria-label="Chat message"></textarea>
                  <button id="sendBtn" class="btn btn-warning" type="button"><i class="bi bi-send"></i></button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    (function() {
      // --- FLOATING ACTION TOGGLE LOGIC ---
      const floatingActions = document.getElementById('floatingActions');
      const toggleBtn = document.getElementById('actionsToggleBtn');
      const toggleIcon = document.getElementById('toggleIcon');

      toggleBtn.addEventListener('click', function() {
        // Toggle the class that handles the hiding/showing via CSS
        floatingActions.classList.toggle('closed');

        // Update Icon based on state
        if (floatingActions.classList.contains('closed')) {
          // If closed, show a 'List' or 'Plus' icon indicating you can open it
          toggleIcon.classList.remove('bi-x-lg');
          toggleIcon.classList.add('bi-list'); // or bi-plus-lg
        } else {
          // If open, show an 'X' to close it
          toggleIcon.classList.remove('bi-list');
          toggleIcon.classList.add('bi-x-lg');
        }
      });

      // --- EXISTING CHAT LOGIC ---
      const launcher = document.getElementById('chatbotLauncher');
      const chatModalEl = document.getElementById('chatModal');
      const chatMessages = document.getElementById('chatMessages');
      const chatInput = document.getElementById('chatInput');
      const sendBtn = document.getElementById('sendBtn');
      const chatForm = document.getElementById('chatForm');

      const chatEndpoint = launcher.getAttribute('href') || 'frontend/chatai.php';
      let modal;

      function appendMessage(text, who = 'bot') {
        const wrapper = document.createElement('div');
        wrapper.className = 'chat-msg ' + (who === 'user' ? 'user' : 'bot');

        if (who === 'user') {
          wrapper.innerHTML = `<div class="chat-bubble">${escapeHtml(text)}</div><div class="avatar">U</div>`;
        } else {
          wrapper.innerHTML = `<div class="avatar">AI</div><div class="chat-bubble">${escapeHtml(text)}</div>`;
        }

        chatMessages.appendChild(wrapper);
        chatMessages.scrollTop = chatMessages.scrollHeight;
      }

      function appendTypingIndicator() {
        const wrap = document.createElement('div');
        wrap.className = 'chat-msg bot typing';
        wrap.innerHTML = `<div class="avatar">AI</div><div class="chat-bubble"><div class="chat-typing" aria-hidden="true"></div></div>`;
        chatMessages.appendChild(wrap);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return wrap;
      }

      function removeNode(node) {
        if (node && node.parentNode) node.parentNode.removeChild(node);
      }

      function escapeHtml(s) {
        return (s + '').replace(/[&<>"']/g, function(m) {
          return ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
          })[m];
        });
      }

      function setSendingState(isSending) {
        chatInput.disabled = isSending;
        sendBtn.disabled = isSending;
        if (!isSending) chatInput.focus();
      }

      launcher.addEventListener('click', function(ev) {
        ev.preventDefault();
        if (!modal) modal = new bootstrap.Modal(chatModalEl, {
          keyboard: true
        });
        modal.show();
        setTimeout(() => chatInput.focus(), 220);
      });

      async function sendMessage(text) {
        if (!text || !text.trim()) return;
        const message = text.trim();
        appendMessage(message, 'user');
        chatInput.value = '';
        setSendingState(true);

        const typingNode = appendTypingIndicator();

        try {
          const fd = new FormData();
          fd.append('prompt', message);

          const res = await fetch(chatEndpoint, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
          });

          const textResp = await res.text();
          removeNode(typingNode);

          if (!res.ok) {
            appendMessage('Sorry, I could not reach the assistant. Try again later.', 'bot');
          } else {
            appendMessage(textResp, 'bot');
          }
        } catch (err) {
          removeNode(typingNode);
          appendMessage('Network error. Please check your connection and try again.', 'bot');
          console.error('Chat error:', err);
        } finally {
          setSendingState(false);
        }
      }

      sendBtn.addEventListener('click', () => {
        const v = chatInput.value;
        sendMessage(v);
      });

      chatInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
          e.preventDefault();
          sendBtn.click();
        }
      });

      chatModalEl.addEventListener('hidden.bs.modal', function() {
        // chatMessages.innerHTML = '';
      });

      launcher.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          launcher.click();
        }
      });

      chatModalEl.addEventListener('shown.bs.modal', function() {
        if (chatMessages.children.length === 0) {
          appendMessage('Hi! I am Pack IT Assistant. How can I help you today?', 'bot');
        }
      });

      document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.reveal').forEach(el => el.classList.add('visible'));
      });

    })();
  </script>
</body>

</html>