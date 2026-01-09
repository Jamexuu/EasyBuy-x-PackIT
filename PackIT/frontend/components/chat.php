<style>
  /* --- CONSOLIDATED CHAT STYLES --- */

  /* Floating Chat Button */
  .poc-chat-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #facc15;
    color: #1f2937;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: transform 0.2s, box-shadow 0.2s;
    z-index: 1060;
  }
  .poc-chat-btn:hover { transform: scale(1.05); box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); }
  .poc-tooltip { position: absolute; right: 70px; top: 50%; transform: translateY(-50%); background: #333; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px; white-space: nowrap; opacity: 0; pointer-events: none; transition: opacity 0.2s; }
  .poc-chat-btn:hover .poc-tooltip { opacity: 1; }

  /* --- CHAT MODAL / WIDGET --- */
  
  /* Desktop: Fixed Widget Position */
  .poc-chat-modal .modal-dialog {
    margin: 0;
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 400px;
    z-index: 1061;
    transition: transform 0.3s ease-out;
  }

  /* Chat Window Container */
  .poc-chat-window {
    height: 500px; /* Default desktop height */
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #fff;
    border-radius: 0.5rem;
  }

  /* --- MOBILE RESPONSIVENESS (Bottom Sheet Style) --- */
  @media (max-width: 576px) {
    .poc-chat-modal {
      padding-right: 0 !important; /* Prevent Bootstrap adding padding */
    }

    .poc-chat-modal .modal-dialog {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      width: 100%;
      max-width: 100%;
      margin: 0;
      transform: none !important; /* Override standard modal transitions if needed */
    }

    .poc-chat-modal .modal-content {
      border-radius: 1.5rem 1.5rem 0 0 !important; /* Round top corners only */
      border: none;
      box-shadow: 0 -5px 25px rgba(0,0,0,0.15);
      height: 85vh; /* Take up 85% of screen height */
      display: flex;
      flex-direction: column;
    }

    .poc-chat-modal .modal-body {
      flex: 1; /* Allow body to grow */
      overflow: hidden; /* Prevent double scrollbars */
      padding: 0;
    }

    /* Force the window inside to fill the modal content */
    .poc-chat-window {
      height: 100% !important; 
      border-radius: 0 !important;
    }
    
    /* Input area safe padding for iPhone home bar */
    .poc-chat-input {
      padding-bottom: max(0.75rem, env(safe-area-inset-bottom));
    }
  }

  .poc-chat-messages {
    flex: 1 1 auto;
    padding: 1rem;
    overflow-y: auto;
    background: linear-gradient(180deg, #f8f9fa, #fff);
    /* Smooth scroll */
    scroll-behavior: smooth;
  }

  .poc-chat-input {
    border-top: 1px solid #dee2e6;
    padding: 0.75rem;
    background: #fff;
    flex-shrink: 0; /* Prevent shrinking */
  }

  /* Message Bubbles */
  .poc-chat-msg {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
    align-items: flex-end;
  }

  .poc-chat-msg .avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    flex-shrink: 0;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.8rem;
    color: #495057;
  }

  .poc-chat-bubble {
    max-width: 75%;
    padding: 0.7rem 1rem;
    border-radius: 1rem;
    font-size: 0.95rem;
    line-height: 1.4;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    word-wrap: break-word;
  }

  .poc-chat-msg.user { justify-content: flex-end; }
  .poc-chat-msg.user .poc-chat-bubble {
    background: #0d6efd;
    color: #fff;
    border-bottom-right-radius: 0.25rem;
  }

  .poc-chat-msg.bot .poc-chat-bubble {
    background: #f1f3f5;
    color: #212529;
    border-bottom-left-radius: 0.25rem;
  }

  .poc-chat-typing {
    width: 30px;
    height: 8px;
    background: linear-gradient(90deg, #dee2e6, #adb5bd);
    border-radius: 4px;
    animation: poc-blink 1.5s infinite;
  }
  @keyframes poc-blink { 0%, 100% { opacity: 0.3; } 50% { opacity: 1; } }

  /* Quick Buttons CSS */
  #packit-quick-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    padding: 10px;
    border-top: 1px solid #eef2ff;
    border-bottom: 1px solid #f1f3f5;
    background: #ffffff;
    justify-content: flex-start;
    align-items: center;
    flex-shrink: 0;
  }
  .packit-quick-btn {
    background: #f3f6ff;
    color: #0d47a1;
    border: 1px solid #d8e4ff;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    white-space: nowrap;
  }
  .packit-quick-btn:hover { background: #e8f0ff; }
</style>

<div class="poc-chat-btn" id="pocChatBtn" role="button" aria-label="Open chat" tabindex="0">
  💬
  <div class="poc-tooltip">Chat with POC</div>
</div>

<div class="modal fade poc-chat-modal" id="pocChatModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
  <div class="modal-dialog shadow-lg rounded-3">
    <div class="modal-content border-0">
      <div class="modal-header bg-light py-2 border-bottom-0 rounded-top-3">
        <div class="d-flex align-items-center gap-2">
          <img src="/EasyBuy-x-PackIT/PackIT/assets/chatbot.png" alt="Bot" width="32" height="32" class="object-fit-contain">
          <div class="lh-1">
            <div class="fw-bold fs-6">PackIT Assistant</div>
            <small class="text-muted" style="font-size: 0.75rem;">Online</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-0">
        <div class="poc-chat-window" role="region" aria-label="Chat messages">
          <div class="poc-chat-messages" id="pocChatMessages" aria-live="polite"></div>

          <div id="packit-quick-buttons">
            <button type="button" class="packit-quick-btn" data-prompt="What is the status of my bookings?">Status of my bookings</button>
            <button type="button" class="packit-quick-btn" data-prompt="Where are my parcels?">Where are my parcels?</button>
            <button type="button" class="packit-quick-btn" data-prompt="How much did my last booking cost?">Last booking cost</button>
            <button type="button" class="packit-quick-btn" data-prompt="How do I cancel a booking?">How to cancel</button>
          </div>

          <div class="poc-chat-input">
            <form id="pocChatForm" onsubmit="return false;">
              <div class="d-flex gap-2 align-items-center">
                <textarea id="pocChatInput" class="form-control form-control-sm" placeholder="Type a message..." aria-label="Message" style="resize: none; height: 42px; font-size: 0.95rem;"></textarea>
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

<script>
(function () {
  const btn = document.getElementById('pocChatBtn');
  const launcher = document.getElementById('chatbotLauncher');
  const modalEl = document.getElementById('pocChatModal');
  const messagesEl = document.getElementById('pocChatMessages');
  const inputEl = document.getElementById('pocChatInput');
  const sendBtn = document.getElementById('pocSendBtn');

  const chatEndpoint = "/EasyBuy-x-PackIT/PackIT/frontend/chatai.php";
  const historyEndpoint = "/EasyBuy-x-PackIT/PackIT/frontend/chat_history.php";

  let bsModal;

  function showChat() {
    if (!modalEl) { window.location.href = chatEndpoint; return; }
    if (!bsModal) bsModal = new bootstrap.Modal(modalEl, { keyboard: true });
    bsModal.show();
  }

  // Handle both the floating button and the launcher in index.php
  [btn, launcher].forEach(el => {
    if (el) {
      el.addEventListener('click', (e) => { e.preventDefault(); showChat(); });
      el.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); showChat(); }
      });
    }
  });

  if (!modalEl || !messagesEl || !inputEl || !sendBtn) return;

  function sanitize(text) {
    return String(text).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  }

  function appendMessage(text, who = 'bot') {
    const wrapper = document.createElement('div');
    wrapper.className = `poc-chat-msg ${who}`;
    const avatar = `<div class="avatar">${who === 'user' ? 'U' : 'AI'}</div>`;
    const bubble = `<div class="poc-chat-bubble">${sanitize(text)}</div>`;
    wrapper.innerHTML = who === 'user' ? (bubble + avatar) : (avatar + bubble);
    messagesEl.appendChild(wrapper);
    messagesEl.scrollTop = messagesEl.scrollHeight;
  }

  function appendTyping() {
    const wrap = document.createElement('div');
    wrap.className = 'poc-chat-msg bot';
    wrap.innerHTML = `<div class="avatar">AI</div><div class="poc-chat-bubble"><div class="poc-chat-typing"></div></div>`;
    messagesEl.appendChild(wrap);
    messagesEl.scrollTop = messagesEl.scrollHeight;
    return wrap;
  }

  modalEl.addEventListener('shown.bs.modal', async function () {
    inputEl.focus();
    if (messagesEl.children.length === 0) {
      try {
        const res = await fetch(historyEndpoint, { cache: 'no-store' });
        const json = await res.json();
        if (json?.success && Array.isArray(json.history) && json.history.length) {
          json.history.forEach(item => {
            appendMessage(item.prompt, 'user');
            appendMessage(item.response, 'bot');
          });
        } else {
          appendMessage('Hi! I am PackIT Assistant. How can I help you today?', 'bot');
        }
      } catch (e) {
        appendMessage('Hi! I am PackIT Assistant. How can I help you today?', 'bot');
      }
    }
  });

  modalEl.addEventListener('hide.bs.modal', () => { messagesEl.innerHTML = ''; });

  async function sendMessage(text) {
    if (!text || !text.trim()) return;
    const msg = text.trim();
    appendMessage(msg, 'user');
    inputEl.value = '';
    inputEl.disabled = true;
    sendBtn.disabled = true;

    const typing = appendTyping();

    try {
      const fd = new FormData();
      fd.append('prompt', msg);
      const res = await fetch(chatEndpoint, { method: 'POST', body: fd, credentials: 'same-origin' });
      const data = await res.json();
      typing.remove();
      if (!res.ok || !data) appendMessage('System error. Please try again.', 'bot');
      else if (!data.success) appendMessage(data.reply ?? data.error ?? 'POC unavailable.', 'bot');
      else appendMessage(data.reply, 'bot');
    } catch (err) {
      typing.remove();
      appendMessage('Network error.', 'bot');
    } finally {
      inputEl.disabled = false;
      sendBtn.disabled = false;
      inputEl.focus();
    }
  }

  window.pocSendMessage = sendMessage;
  sendBtn.addEventListener('click', () => sendMessage(inputEl.value));
  inputEl.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(inputEl.value); }
  });

  // Quick Buttons handler
  document.addEventListener('click', function(e){
    if (e.target.classList.contains('packit-quick-btn')) {
      const prompt = e.target.getAttribute('data-prompt');
      showChat();
      if (typeof window.pocSendMessage === 'function') window.pocSendMessage(prompt);
    }
  });

  window.showChat = showChat;
})();
</script>