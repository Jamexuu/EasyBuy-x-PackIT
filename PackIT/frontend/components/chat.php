<?php
// frontend/components/chat.php
// Chat launcher + modal (fixed caption visibility bug)
//
// Fixes included in this version:
// - Uses the provided chatbot image: /EasyBuy-x-PackIT/PackIT/assets/chatbot.png
// - "Need Help?" caption appears only on hover/focus (no background) and is hidden while the modal is open.
// - Caption visibility is controlled via a CSS class on the launcher instead of inline styles so it reliably
//   returns after the modal closes (no hard refresh required).
// - User message bubbles are yellow with dark text (unchanged from your last working version).
//
// Replace your current frontend/components/chat.php with this file.
?>
<style>
  /* --- LAUNCHER: button + caption --- */
  .poc-chat-launcher {
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    z-index: 1060;
    pointer-events: auto;
  }

  /* Caption hidden by default; becomes visible when launcher is hovered/focused */
  .poc-chat-caption {
    font-size: 0.85rem;
    color: #111827;
    padding: 0;
    margin: 0;
    line-height: 1;
    cursor: pointer;
    user-select: none;
    text-align: center;
    opacity: 0;
    transform: translateY(6px);
    transition: opacity 0.12s ease, transform 0.12s ease;
    pointer-events: none;
  }

  /* Show caption on hover or keyboard focus inside launcher */
  .poc-chat-launcher:hover .poc-chat-caption,
  .poc-chat-launcher:focus-within .poc-chat-caption {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
  }

  /* When the chat is open, apply a state class to hide the caption (no inline styles needed) */
  .poc-chat-launcher.chat-open .poc-chat-caption {
    opacity: 0 !important;
    transform: translateY(6px) !important;
    pointer-events: none !important;
  }
  .poc-chat-launcher.chat-open .poc-chat-btn {
    transform: scale(0.98);
  }

  @media (max-width: 576px) {
    /* Hide caption on extra-small screens to keep original compact appearance */
    .poc-chat-caption { display: none; }
  }

  /* --- Floating Chat Button (uses project image) --- */
  .poc-chat-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #facc15;
    color: #1f2937;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: transform 0.2s, box-shadow 0.2s, opacity 0.3s;
    border: 0;
    overflow: hidden;
  }
  .poc-chat-btn img {
    width: 34px;
    height: 34px;
    object-fit: contain;
    display: block;
  }
  .poc-chat-btn:hover { transform: scale(1.05); box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); }
  .poc-chat-btn.hidden { opacity: 0; pointer-events: none; }

  /* --- CHAT MODAL / WIDGET --- */
  .poc-chat-modal .modal-dialog {
    margin: 0;
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 400px;
    max-width: 90vw; 
    z-index: 1061;
    transition: transform 0.3s ease-out;
  }

  .poc-chat-window {
    height: 500px; 
    max-height: 65vh; 
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #fff;
    border-radius: 0.5rem;
  }

  @media (max-width: 768px) {
    .poc-chat-modal { padding-right: 0 !important; }

    .poc-chat-modal .modal-dialog {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      width: 100%;
      max-width: 100%;
      margin: 0;
    }

    .poc-chat-modal .modal-content {
      border-radius: 1.5rem 1.5rem 0 0 !important;
      border: none;
      box-shadow: 0 -5px 25px rgba(0,0,0,0.15);
      height: 80dvh; 
      max-height: 80dvh;
      display: flex;
      flex-direction: column;
    }

    .poc-chat-modal .modal-body { flex: 1; overflow: hidden; padding: 0; }

    .poc-chat-window {
      height: 100% !important; 
      max-height: none !important;
      border-radius: 0 !important;
    }
    
    .poc-chat-input { padding-bottom: max(0.75rem, env(safe-area-inset-bottom)); }
  }

  .poc-chat-messages {
    flex: 1 1 auto;
    padding: 1rem;
    overflow-y: auto;
    background: linear-gradient(180deg, #f8f9fa, #fff);
    scroll-behavior: smooth;
  }

  .poc-chat-input {
    border-top: 1px solid #dee2e6;
    padding: 0.75rem;
    background: #fff;
    flex-shrink: 0; 
  }

  /* Message Bubbles */
  .poc-chat-msg { display: flex; gap: 0.5rem; margin-bottom: 1rem; align-items: flex-end; }

  .poc-chat-msg .avatar {
    width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
    background: #e9ecef; display: flex; align-items: center; justify-content: center;
    font-weight: bold; font-size: 0.8rem; color: #495057;
  }

  .poc-chat-bubble {
    max-width: 75%; padding: 0.7rem 1rem; border-radius: 1rem;
    font-size: 0.95rem; line-height: 1.4; box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    word-wrap: break-word;
  }

  /* User bubble: yellow with dark text */
  .poc-chat-msg.user { justify-content: flex-end; }
  .poc-chat-msg.user .poc-chat-bubble { background: #facc15; color: #1f2937; border-bottom-right-radius: 0.25rem; }

  /* Bot bubble unchanged (light) */
  .poc-chat-msg.bot .poc-chat-bubble { background: #f1f3f5; color: #212529; border-bottom-left-radius: 0.25rem; }

  .poc-chat-typing {
    width: 30px; height: 8px; background: linear-gradient(90deg, #dee2e6, #adb5bd);
    border-radius: 4px; animation: poc-blink 1.5s infinite;
  }
  @keyframes poc-blink { 0%, 100% { opacity: 0.3; } 50% { opacity: 1; } }

  /* Quick Buttons CSS */
  #packit-quick-buttons {
    display: flex; gap: 8px; padding: 10px;
    border-top: 1px solid #eef2ff; border-bottom: 1px solid #f1f3f5;
    background: #ffffff; align-items: center; flex-shrink: 0;
    flex-wrap: wrap; 
  }

  @media (max-width: 768px) {
    #packit-quick-buttons {
      flex-wrap: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch;
      padding-bottom: 12px; scrollbar-width: none;
    }
    #packit-quick-buttons::-webkit-scrollbar { display: none; }
  }

  .packit-quick-btn {
    background: #f3f6ff; color: #0d47a1; border: 1px solid #d8e4ff;
    padding: 6px 12px; border-radius: 20px; cursor: pointer;
    font-size: 13px; white-space: nowrap; transition: background 0.2s;
  }
  .packit-quick-btn:hover { background: #e8f0ff; }

  .mobile-drag-handle {
    display: none; width: 40px; height: 5px; background-color: #dee2e6;
    border-radius: 10px; margin: 8px auto;
  }
  @media (max-width: 768px) {
    .mobile-drag-handle { display: block; }
  }
</style>

<!-- Launcher: circular button with caption below (caption shows on hover/focus; hidden while chat is open) -->
<div class="poc-chat-launcher" id="pocChatLauncher" aria-hidden="false">
  <div class="poc-chat-btn" id="pocChatBtn" role="button" aria-label="Open chat" tabindex="0">
    <img src="/EasyBuy-x-PackIT/PackIT/assets/chatbot.png" alt="Chatbot">
  </div>

  <!-- Simple caption below the icon (hidden by default; appears on hover/focus) -->
  <div class="poc-chat-caption" id="pocChatCaption" role="button" tabindex="0" aria-label="Need Help? Open chat">
    Need Help?
  </div>
</div>

<!-- Chat modal (unchanged behavior) -->
<div class="modal fade poc-chat-modal" id="pocChatModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
  <div class="modal-dialog shadow-lg rounded-3">
    <div class="modal-content border-0">
      
      <div class="mobile-drag-handle"></div>

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
                <button id="pocSendBtn" class="btn btn-warning btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" type="button">
                  <i class="bi bi-send-fill" aria-hidden="true"></i>
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
  const launcher = document.getElementById('pocChatLauncher');
  const btn = document.getElementById('pocChatBtn');
  const caption = document.getElementById('pocChatCaption');
  const modalEl = document.getElementById('pocChatModal');
  const messagesEl = document.getElementById('pocChatMessages');
  const inputEl = document.getElementById('pocChatInput');
  const sendBtn = document.getElementById('pocSendBtn');

  // Paths
  const chatEndpoint = "/EasyBuy-x-PackIT/PackIT/frontend/chatai.php";
  const historyEndpoint = "/EasyBuy-x-PackIT/PackIT/frontend/chat_history.php";

  // Default model to request (frontend will include this; backend must honor it)
  const DEFAULT_MODEL = 'gemma2:2b';

  let bsModal;

  function showChat() {
    if (!modalEl) { window.location.href = chatEndpoint; return; }
    if (!bsModal) bsModal = new bootstrap.Modal(modalEl, { keyboard: true });
    bsModal.show();
  }

  // Make both the circular button AND the caption open the chat (click and keyboard)
  [btn, caption].forEach(el => {
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
    const avatar = `<div class="avatar">${who === 'user' ? '<i class="bi bi-person"></i>' : '<i class="bi bi-robot"></i>'}</div>`;
    const bubble = `<div class="poc-chat-bubble">${sanitize(text)}</div>`;
    wrapper.innerHTML = who === 'user' ? (bubble + avatar) : (avatar + bubble);
    messagesEl.appendChild(wrapper);
    messagesEl.scrollTop = messagesEl.scrollHeight;
  }

  function appendTyping() {
    const wrap = document.createElement('div');
    wrap.className = 'poc-chat-msg bot';
    wrap.innerHTML = `<div class="avatar"><i class="bi bi-robot"></i></div><div class="poc-chat-bubble"><div class="poc-chat-typing"></div></div>`;
    messagesEl.appendChild(wrap);
    messagesEl.scrollTop = messagesEl.scrollHeight;
    return wrap;
  }

  // Modal Events
  modalEl.addEventListener('show.bs.modal', function () {
    // Indicate chat is open: hide the floating button and hide caption via launcher state class
    if (btn) btn.classList.add('hidden');
    if (launcher) launcher.classList.add('chat-open');
  });

  modalEl.addEventListener('shown.bs.modal', async function () {
    inputEl.focus();
    // Load history if empty
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

  modalEl.addEventListener('hide.bs.modal', () => {
    // Clear messages and restore launcher state so caption reliably reappears
    messagesEl.innerHTML = '';
    if (btn) btn.classList.remove('hidden');
    if (launcher) launcher.classList.remove('chat-open');
    // return focus to button for accessibility
    if (btn) btn.focus();
  });

  async function sendMessage(text, model = DEFAULT_MODEL) {
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
      fd.append('model', model);

      const res = await fetch(chatEndpoint, { method: 'POST', body: fd, credentials: 'same-origin' });
      const data = await res.json().catch(() => null);
      typing.remove();

      if (!res.ok || !data) {
        appendMessage('System error. Please try again.', 'bot');
      } else if (!data.success) {
        appendMessage(data.reply ?? data.error ?? 'gemma2:2b unavailable.', 'bot');
      } else {
        appendMessage(data.reply, 'bot');
      }
    } catch (err) {
      typing.remove();
      appendMessage('Network error.', 'bot');
    } finally {
      inputEl.disabled = false;
      sendBtn.disabled = false;
      inputEl.focus();
    }
  }

  // expose both new and legacy names
  window.gemmaSendMessage = sendMessage;
  window.pocSendMessage = sendMessage;

  sendBtn.addEventListener('click', () => sendMessage(inputEl.value));
  inputEl.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(inputEl.value); }
  });

  // Quick Buttons handler
  document.addEventListener('click', function(e){
    const target = e.target.closest('.packit-quick-btn');
    if (target) {
      const prompt = target.getAttribute('data-prompt');
      showChat();
      if (typeof window.gemmaSendMessage === 'function') window.gemmaSendMessage(prompt);
    }
  });

  window.showChat = showChat;
})();
</script>