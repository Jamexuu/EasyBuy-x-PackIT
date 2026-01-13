<?php
// Chat widget (frontend/components/chat.php)
// Updated to use model "gemma2:2b" by default from the frontend (backend must honour it).
// Kept same UI structure; small JS changes: send 'model' in the POST, update text references from "POC" to "gemma2:2b",
// and expose both window.gemmaSendMessage and window.pocSendMessage for backward compatibility.
?>

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
    transition: transform 0.2s, box-shadow 0.2s, opacity 0.3s;
    z-index: 1060;
  }
  .poc-chat-btn:hover { transform: scale(1.05); box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); }
  
  .poc-chat-btn.hidden { opacity: 0; pointer-events: none; }
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
    /* FIX: Prevent overflow on zoomed screens */
    max-width: 90vw; 
    z-index: 1061;
    transition: transform 0.3s ease-out;
  }

  /* Chat Window Container */
  .poc-chat-window {
    /* FIX: Use vh (viewport height) so it shrinks if you zoom in */
    height: 500px; 
    max-height: 65vh; 
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #fff;
    border-radius: 0.5rem;
  }

  /* --- MOBILE / TABLET / ZOOMED RESPONSIVENESS --- */
  /* FIX: Increased breakpoint to 768px (Tablets) so it switches to mobile view sooner */
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
      /* Use dvh for mobile browsers */
      height: 80dvh; 
      max-height: 80dvh;
      display: flex;
      flex-direction: column;
    }

    .poc-chat-modal .modal-body { flex: 1; overflow: hidden; padding: 0; }

    /* Override the desktop constraints for mobile */
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

  .poc-chat-msg.user { justify-content: flex-end; }
  .poc-chat-msg.user .poc-chat-bubble { background: #0d6efd; color: #fff; border-bottom-right-radius: 0.25rem; }

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

  /* Mobile/Tablet Specific for Quick Buttons: Horizontal Scroll */
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

<div class="poc-chat-btn" id="pocChatBtn" role="button" aria-label="Open chat" tabindex="0">
  <i class="bi bi-chat-text-fill"></i>
</div>

<div class="modal fade poc-chat-modal" id="pocChatModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
  <div class="modal-dialog shadow-lg rounded-3">
    <div class="modal-content border-0">
      
      <div class="mobile-drag-handle"></div>

      <div class="modal-header bg-light py-2 border-bottom-0 rounded-top-3">
        <div class="d-flex align-items-center gap-2">
          <img src="/EasyBuy-x-PackIT/PackIT/assets/chatbot.png" alt="Bot" width="32" height="32" class="object-fit-contain">
          <div class="lh-1">
            <div class="fw-bold fs-6">gemma2:2b</div>
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
  const launcher = document.getElementById('chatbotLauncher'); // In case it exists in index
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

  // Handle launchers
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
    // Hide floating button when chat opens
    if(btn) btn.classList.add('hidden');
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
          appendMessage('Hi! I am gemma2:2b. How can I help you today?', 'bot');
        }
      } catch (e) {
        appendMessage('Hi! I am gemma2:2b. How can I help you today?', 'bot');
      }
    }
  });

  modalEl.addEventListener('hide.bs.modal', () => { 
    messagesEl.innerHTML = ''; 
    // Show floating button again
    if(btn) btn.classList.remove('hidden');
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
      // include model preference so backend can route to the correct model if it supports it
      fd.append('model', model);

      const res = await fetch(chatEndpoint, { method: 'POST', body: fd, credentials: 'same-origin' });
      const data = await res.json().catch(() => null);
      typing.remove();

      if (!res.ok || !data) {
        appendMessage('System error. Please try again.', 'bot');
      } else if (!data.success) {
        // Friendly message referencing new assistant name
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