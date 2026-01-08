<?php
// chatModal.php - Integrated chat modal with embedded quick-buttons (self-contained).
// Backup your existing chatModal.php before replacing it with this file.
?>
<style>
  /* --- INTEGRATED CHAT STYLES --- */

  /* Desktop: Fixed Widget Position */
  .poc-chat-modal .modal-dialog {
    margin: 0;
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 400px;
    z-index: 1061;
    transition: transform 0.3s ease-out;
  }

  /* Mobile: Bottom Sheet / Drawer Style */
  @media (max-width: 576px) {
    .poc-chat-modal .modal-dialog {
      bottom: 0;
      right: 0;
      left: 0;
      width: 100%;
      max-width: none;
      margin: 0;
    }

    .poc-chat-modal .modal-content {
      border-radius: 1.5rem 1.5rem 0 0 !important;
      border: none;
      box-shadow: 0 -10px 40px rgba(0,0,0,0.1);
      height: 80vh;
      display: flex;
      flex-direction: column;
    }

    .poc-chat-window { height: 100% !important; border-radius: 0 !important; }
  }

  .poc-chat-window {
    height: 500px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #fff;
    border-radius: 0.5rem;
  }

  .poc-chat-messages {
    flex: 1 1 auto;
    padding: 1rem;
    overflow-y: auto;
    background: linear-gradient(180deg, #f8f9fa, #fff);
  }

  .poc-chat-input {
    border-top: 1px solid #dee2e6;
    padding: 0.75rem;
    background: #fff;
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

  /* User Message Styles */
  .poc-chat-msg.user { justify-content: flex-end; }
  .poc-chat-msg.user .poc-chat-bubble {
    background: #0d6efd;
    color: #fff;
    border-bottom-right-radius: 0.25rem;
  }

  /* Bot Message Styles */
  .poc-chat-msg.bot .poc-chat-bubble {
    background: #f1f3f5;
    color: #212529;
    border-bottom-left-radius: 0.25rem;
  }

  /* Typing Indicator */
  .poc-chat-typing {
    width: 30px;
    height: 8px;
    background: linear-gradient(90deg, #dee2e6, #adb5bd);
    border-radius: 4px;
    animation: poc-blink 1.5s infinite;
  }
  @keyframes poc-blink { 0%, 100% { opacity: 0.3; } 50% { opacity: 1; } }

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

  /* === Embedded Quick Buttons CSS (non-floating) === */
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
  }
  .packit-quick-btn {
    background: #f3f6ff;
    color: #0d47a1;
    border: 1px solid #d8e4ff;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
  }
  .packit-quick-btn:hover { background: #e8f0ff; }
  .packit-quick-reply {
    margin-top: 10px;
    background: #ffffff;
    border-radius: 6px;
    padding: 8px 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    color: #111;
    font-size: 14px;
    max-width: 100%;
    word-wrap: break-word;
    display: none;
  }
  .packit-quick-loading { color: #6c757d; font-style: italic; }

  @media (max-width: 576px) {
    #packit-quick-buttons { padding: 8px; gap: 6px; }
    .packit-quick-btn { padding: 6px 8px; font-size: 12px; }
  }
</style>

<!-- Chat trigger button -->
<div class="poc-chat-btn" id="pocChatBtn" role="button" aria-label="Open chat" tabindex="0">
  💬
  <div class="poc-tooltip">Chat with POC</div>
</div>

<!-- Chat modal -->
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

          <!-- QUICK BUTTONS: placed directly above the message input -->
          <div id="packit-quick-buttons" aria-hidden="false">
            <button type="button" class="packit-quick-btn" data-prompt="What is the status of my bookings?">Status of my bookings</button>
            <button type="button" class="packit-quick-btn" data-prompt="Where are my parcels?">Where are my parcels?</button>
            <button type="button" class="packit-quick-btn" data-prompt="How much did my last booking cost?">Last booking cost</button>
            <button type="button" class="packit-quick-btn" data-prompt="How do I cancel a booking?">How to cancel</button>
          </div>
          <div id="packit-quick-reply" class="packit-quick-reply"></div>

          <div class="poc-chat-input">
            <form id="pocChatForm" onsubmit="return false;">
              <div class="d-flex gap-2 align-items-center">
                <textarea id="pocChatInput" class="form-control form-control-sm" placeholder="Type a message..." aria-label="Message"></textarea>
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
  const modalEl = document.getElementById('pocChatModal');
  const messagesEl = document.getElementById('pocChatMessages');
  const inputEl = document.getElementById('pocChatInput');
  const sendBtn = document.getElementById('pocSendBtn');

  // Endpoints (adjust paths if project root differs)
  const chatEndpoint = "/EasyBuy-x-PackIT/PackIT/frontend/chatai.php";
  const historyEndpoint = "/EasyBuy-x-PackIT/PackIT/frontend/chat_history.php";

  function goToChat() {
    if (!modalEl) { window.location.href = chatEndpoint; return; }
    const modal = new bootstrap.Modal(modalEl, { keyboard: true });
    modal.show();
  }

  if (btn) {
    btn.addEventListener('click', (e) => { e.preventDefault(); goToChat(); });
    btn.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); goToChat(); }
    });
  }

  if (!modalEl || !messagesEl || !inputEl || !sendBtn) return;

  function sanitizeForHtml(text) {
    if (text === null || text === undefined) return '';
    return String(text).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  }

  function appendMessage(text, who = 'bot') {
    const wrapper = document.createElement('div');
    wrapper.className = `poc-chat-msg ${who}`;
    const avatar = `<div class="avatar">${who === 'user' ? 'U' : 'AI'}</div>`;
    const bubble = `<div class="poc-chat-bubble">${sanitizeForHtml(text)}</div>`;
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

  // Fetch history when modal is shown
  modalEl.addEventListener('shown.bs.modal', async function () {
    inputEl.focus();
    // Only load once per open for speed (cleared on hide)
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
        console.error('Failed to load chat history', e);
        appendMessage('Hi! I am PackIT Assistant. How can I help you today?', 'bot');
      }
    }
  });

  // Clear messages when closed so it reloads fresh next open (optional)
  modalEl.addEventListener('hide.bs.modal', function () {
    messagesEl.innerHTML = '';
  });

  // Send logic
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

      const res = await fetch(chatEndpoint, { method: 'POST', body: fd, credentials: 'same-origin' });
      let data;
      try { data = await res.json(); } catch (e) { data = null; }

      typingNode.remove();

      if (!res.ok || !data) {
        appendMessage('System error. Please try again.', 'bot');
      } else if (!data.success) {
        appendMessage(data.reply ?? data.error ?? 'POC is unavailable right now.', 'bot');
      } else {
        appendMessage(data.reply, 'bot');
      }
    } catch (err) {
      typingNode.remove();
      appendMessage('Network error. Check connection.', 'bot');
      console.error(err);
    } finally {
      inputEl.disabled = false;
      sendBtn.disabled = false;
      inputEl.focus();
    }
  }

  // Expose the internal sendMessage for external callers (quick buttons)
  window.pocSendMessage = sendMessage;

  // Expose for debugging
  window.goToChat = goToChat;

  // Attach main UI send controls
  sendBtn.addEventListener('click', () => sendMessage(inputEl.value));
  inputEl.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendBtn.click();
    }
  });
})();
</script>

<!-- Quick Buttons handler (embedded, runs after main chat script) -->
<script>
(function(){
  document.addEventListener('click', function(e){
    var t = e.target;
    if (!t) return;
    if (t.classList && t.classList.contains('packit-quick-btn')) {
      var prompt = t.getAttribute('data-prompt') || t.textContent || '';

      // show modal if not visible
      try {
        var modalEl = document.getElementById('pocChatModal');
        if (modalEl) {
          var bs = null;
          try { bs = bootstrap.Modal.getInstance(modalEl); } catch(ex) { bs = null; }
          if (!bs) { new bootstrap.Modal(modalEl).show(); }
        }
      } catch(ignore){}

      // Use the modal's send function if available, so message appears inside chat UI
      if (typeof window.pocSendMessage === 'function') {
        window.pocSendMessage(prompt);
        return;
      }

      // Fallback: show inline quick reply under buttons
      var replyEl = document.getElementById('packit-quick-reply');
      if (!replyEl) return;
      replyEl.style.display = 'block';
      replyEl.innerHTML = '<span class="packit-quick-loading">Thinking...</span>';

      var fd = new FormData();
      fd.append('prompt', prompt);

      fetch('/EasyBuy-x-PackIT/PackIT/frontend/chatai.php', {
        method: 'POST',
        body: fd,
        credentials: 'same-origin'
      }).then(function(res){ return res.json(); }).then(function(json){
        if (json && json.reply) {
          replyEl.textContent = json.reply;
        } else if (json && json.error) {
          replyEl.textContent = 'Error: ' + (json.error || 'Unknown error');
        } else {
          replyEl.textContent = 'No reply from server.';
        }
      }).catch(function(err){
        replyEl.textContent = 'Network error. Please try again.';
        console.error(err);
      });
    }
  });
})();
</script>