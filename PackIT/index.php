<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pack IT</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root {
      --brand-yellow: #f8e15b;
      --brand-dark: #111;
      --brand-gray: #555;
      --soft-shadow: 0 8px 28px rgba(16, 24, 40, 0.06);
      --glass: rgba(255,255,255,0.6);
    }

    /* Base */
    html, body { height: 100%; }
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

    /* Get Started button — keep and subtly enhance */
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
      box-shadow: 0 14px 40px rgba(16,24,40,0.10);
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
    .hero-img:hover { transform: translateY(-6px) scale(1.02); }

    /* Floating actions simplified visuals (keeps same markup) */
    .floating-actions {
      position: fixed;
      top: 50%;
      right: 20px;
      transform: translateY(-50%);
      background: rgba(248,225,91,0.98);
      border-radius: 12px;
      padding: 14px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 14px;
      box-shadow: 0 10px 30px rgba(16,24,40,0.09);
      z-index: 1050;
      min-width: 92px;
      justify-content: center;
    }

    .floating-actions a {
      display: inline-flex;
      flex-direction: column;
      align-items: center;
      text-decoration: none;
      color: var(--brand-dark);
      font-size: 0.9rem;
      transition: transform .14s ease, filter .14s ease;
      gap: 6px;
    }
    .floating-actions a img {
      width: 44px;
      height: 44px;
      object-fit: contain;
      filter: drop-shadow(0 6px 16px rgba(0,0,0,0.08));
    }
    .floating-actions a:hover { transform: translateY(-6px); filter: brightness(1.03); }

    @media (max-width: 991px) {
      h1.display-1 { font-size: 3rem; }
      .floating-actions {
        top: auto;
        bottom: 80px;
        right: 20px;
        flex-direction: row;
        border-radius: 14px;
        padding: 10px;
        gap: 12px;
      }
      .floating-actions a img { width: 36px; height: 36px; }
    }

    /* Chat modal/widget styling kept but refined */
    .chat-widget .modal-dialog {
      max-width: 420px;
      margin: 0;
      position: fixed;
      right: 20px;
      bottom: 90px;
      transform: translateZ(0);
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
      box-shadow: 0 1px 2px rgba(0,0,0,0.03);
      line-height: 1.3;
      white-space: pre-wrap;
      background: #f1f3ff;
    }

    .chat-msg.user { justify-content: flex-end; }
    .chat-msg.user .chat-bubble {
      background: #0d6efd;
      color: #fff;
      border-bottom-right-radius: 4px;
    }
    .chat-msg.bot .chat-bubble { background: #f1f3ff; color: #111; border-bottom-left-radius: 4px; }

    .chat-typing {
      display: inline-block;
      width: 36px;
      height: 12px;
      background: linear-gradient(90deg, #e1e4ff, #c6d0ff);
      border-radius: 6px;
      animation: blink 1.2s infinite;
    }
    @keyframes blink {
      0% { opacity: .25; transform: translateY(0); }
      50% { opacity: 1; transform: translateY(-2px); }
      100% { opacity: .25; transform: translateY(0); }
    }

    .chat-controls { display: flex; gap: 8px; align-items: center; }
    .chat-controls textarea { resize: none; height: 46px; }

    /* Footer curve (kept) */
    .footer-curve {
      height: 90px;
      background: var(--brand-yellow);
      clip-path: ellipse(85% 100% at 50% 100%);
    }

    /* small reveal */
    .reveal { opacity: 0; transform: translateY(10px); transition: opacity .45s ease, transform .45s ease; }
    .reveal.visible { opacity: 1; transform: none; }
  </style>
</head>

<body id="top" class="min-vh-100">

  <?php $page = basename($_SERVER['PHP_SELF']); ?>
  <?php include("frontend/components/navbar.php"); ?>

  <main class="container my-5 py-lg-5">
    <div class="row align-items-center gy-5">
      <div class="col-lg-6 text-center text-lg-start">
        <h1 class="display-1 fw-black text-uppercase">PACK IT</h1>
        <p class="lead mt-4" style="color: var(--brand-gray);">
          The gold standard of PH logistics. 🏆<br>
          Bridging gaps and breaking records, one delivery at a time.
        </p>
        <a href="../PackIT/frontend/aboutUs.php" class="btn bg-brand btn-lg fw-bold rounded-pill px-4 mt-3 hover-scale">Get Started</a>
      </div>

      <div class="col-lg-6 text-center">
        <img src="assets/mascot.png" class="img-fluid hero-img" alt="Mascot" style="max-height: 450px;">
      </div>
    </div>
  </main>

  <div class="floating-actions" aria-hidden="false">
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

  <?php include("frontend/components/footer.php"); ?>

  <!-- Chat Modal (widget) -->
  <div class="modal fade chat-widget" id="chatModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <div class="d-flex align-items-center gap-2">
            <img src="assets/chatbot.png" alt="bot" style="width:36px;height:36px;">
            <div>
              <div class="fw-bold">Pack IT Assistant</div>
              <div class="muted-sm" style="font-size:0.85rem;color:#6c757d">Ask about bookings, tracking, and packaging</div>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <div class="chat-window">
            <div class="chat-messages" id="chatMessages" aria-live="polite" aria-atomic="false">
              <!-- messages will be appended here -->
            </div>

            <div class="chat-input">
              <form id="chatForm" onsubmit="return false;">
                <div class="chat-controls">
                  <textarea id="chatInput" class="form-control" placeholder="Type your message and press Enter to send" aria-label="Chat message"></textarea>
                  <button id="sendBtn" class="btn btn-primary" type="button"><i class="bi bi-send"></i></button>
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
      // Keep the original markup intact. We'll intercept the chatbot anchor click and open a modal chat widget instead.
      const launcher = document.getElementById('chatbotLauncher');
      const chatModalEl = document.getElementById('chatModal');
      const chatMessages = document.getElementById('chatMessages');
      const chatInput = document.getElementById('chatInput');
      const sendBtn = document.getElementById('sendBtn');
      const chatForm = document.getElementById('chatForm');

      // Resolve the backend endpoint from the anchor's href so we don't hardcode a path change.
      const chatEndpoint = launcher.getAttribute('href') || 'frontend/chatai.php';
      let modal;

      // Helper: append message
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

      // Intercept link navigation and open modal
      launcher.addEventListener('click', function(ev) {
        ev.preventDefault();
        // show bootstrap modal positioned at bottom-right
        if (!modal) modal = new bootstrap.Modal(chatModalEl, {
          keyboard: true
        });
        modal.show();
        // focus input
        setTimeout(() => chatInput.focus(), 220);
      });

      // Send message to backend
      async function sendMessage(text) {
        if (!text || !text.trim()) return;
        const message = text.trim();
        // append user message
        appendMessage(message, 'user');
        chatInput.value = '';
        setSendingState(true);

        // show typing
        const typingNode = appendTypingIndicator();

        try {
          // POST form data (chatai.php expects 'prompt' via POST)
          const fd = new FormData();
          fd.append('prompt', message);

          const res = await fetch(chatEndpoint, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
          });

          const textResp = await res.text();

          // remove typing indicator
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

      // UI handlers
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

      // Optionally handle modal hide to clear chat or keep history
      chatModalEl.addEventListener('hidden.bs.modal', function() {
        // Keep conversation by default. If you want to clear on close, uncomment:
        // chatMessages.innerHTML = '';
      });

      // Accessibility: allow opening chat with keyboard when focused
      launcher.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          launcher.click();
        }
      });

      // Prepopulate a welcome message when the modal is opened first time
      chatModalEl.addEventListener('shown.bs.modal', function() {
        if (chatMessages.children.length === 0) {
          appendMessage('Hi! I am Pack IT Assistant. How can I help you today?', 'bot');
        }
      });

      // reveal animation on load for elements that have the class
      document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.reveal').forEach(el => el.classList.add('visible'));
      });

    })();
  </script>
</body>

</html>