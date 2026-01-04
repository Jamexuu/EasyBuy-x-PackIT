<style>
  /* Chat Button Styles */
  .poc-chat-btn {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #facc15;
    color: #1f2937;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    transition: transform 0.2s, box-shadow 0.2s;
    z-index: 1100;
  }

  .poc-chat-btn:hover {
    transform: scale(1.08);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.3);
  }

  .poc-tooltip {
    position: absolute;
    bottom: 75px;
    right: 0;
    background: #111827;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s;
  }

  .poc-chat-btn:hover .poc-tooltip {
    opacity: 1;
  }

  /* Chat modal / widget styles (matches index.php design) */
  .poc-chat-modal .modal-dialog {
    max-width: 420px;
    margin: 0;
    position: fixed;
    right: 20px;
    bottom: 90px;
    transform: translateZ(0);
  }

  .poc-chat-window {
    height: 520px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .poc-chat-messages {
    flex: 1 1 auto;
    padding: 12px;
    overflow-y: auto;
    background: linear-gradient(180deg,#f8f9fa,#fff);
  }

  .poc-chat-input {
    border-top: 1px solid #e9ecef;
    padding: 10px;
    background: #fff;
  }

  .poc-chat-msg {
    display: flex;
    gap: 8px;
    margin-bottom: 10px;
    align-items: flex-end;
  }

  .poc-chat-msg .avatar {
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

  .poc-chat-bubble {
    max-width: 78%;
    padding: 9px 12px;
    border-radius: 12px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    line-height: 1.3;
    white-space: pre-wrap;
  }

  .poc-chat-msg.user { justify-content: flex-end; }

  .poc-chat-msg.user .poc-chat-bubble {
    background: #0d6efd;
    color: #fff;
    border-bottom-right-radius: 4px;
  }

  .poc-chat-msg.bot .poc-chat-bubble {
    background: #f1f3ff;
    color: #111;
    border-bottom-left-radius: 4px;
  }

  .poc-chat-typing {
    display: inline-block;
    width: 36px;
    height: 12px;
    background: linear-gradient(90deg,#e1e4ff,#c6d0ff);
    border-radius: 6px;
    animation: poc-blink 1.2s infinite;
  }

  @keyframes poc-blink {
    0% { opacity: .25; transform: translateY(0); }
    50% { opacity: 1; transform: translateY(-2px); }
    100% { opacity: .25; transform: translateY(0); }
  }

  .poc-chat-controls { display:flex; gap:8px; align-items:center; }
  .poc-chat-controls textarea { resize: none; height: 46px; }
</style>

<!-- Floating chat button (visible by default). Modal remains hidden until user clicks button. -->
<div class="poc-chat-btn" id="pocChatBtn" role="button" aria-label="Open chat with POC" tabindex="0">
  💬
  <div class="poc-tooltip">Chat with POC</div>
</div>

<!-- Chat Modal (widget) - hidden by default, opens only when user clicks the floating button -->
<div class="modal fade poc-chat-modal" id="pocChatModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <div class="d-flex align-items-center gap-2">
          <img src="/EasyBuy-x-PackIT/PackIT/assets/chatbot.png" alt="bot" style="width:36px;height:36px;">
          <div>
            <div class="fw-bold">Pack IT Assistant</div>
            <div class="muted-sm" style="font-size:0.85rem;color:#6c757d">Ask about bookings, tracking, and packaging</div>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-0">
        <div class="poc-chat-window" role="region" aria-label="Chat window">
          <div class="poc-chat-messages" id="pocChatMessages" aria-live="polite" aria-atomic="false">
            <!-- messages will be appended here -->
          </div>

          <div class="poc-chat-input">
            <form id="pocChatForm" onsubmit="return false;">
              <div class="poc-chat-controls">
                <textarea id="pocChatInput" class="form-control" placeholder="Type your message and press Enter to send" aria-label="Chat message"></textarea>
                <button id="pocSendBtn" class="btn btn-primary" type="button"><i class="bi bi-send"></i></button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  /**
   * Behavior:
   * - The floating chat icon (pocChatBtn) is visible by default.
   * - The chat modal (pocChatModal) is hidden by default and will open ONLY when the floating icon is clicked.
   * - No automatic opening or redirection occurs.
   * - The goToChat() function exists so any existing code that calls it will open the modal (instead of navigating).
   */

  function goToChat() {
    const modalEl = document.getElementById('pocChatModal');
    if (!modalEl) {
      // Fallback: if modal not in DOM for some reason, optionally navigate to chatai.php
      // But per request, we prefer opening popup; fallback kept minimal.
      window.location.href = "/EasyBuy-x-PackIT/PackIT/frontend/chatai.php";
      return;
    }
    const modal = new bootstrap.Modal(modalEl, { keyboard: true });
    modal.show();
    setTimeout(() => {
      const input = document.getElementById('pocChatInput');
      if (input) input.focus();
    }, 220);
  }

  (function () {
    const btn = document.getElementById('pocChatBtn');
    const modalEl = document.getElementById('pocChatModal');
    const messagesEl = document.getElementById('pocChatMessages');
    const inputEl = document.getElementById('pocChatInput');
    const sendBtn = document.getElementById('pocSendBtn');
    const chatEndpoint = "/EasyBuy-x-PackIT/PackIT/frontend/chatai.php"; // backend endpoint (POST 'prompt')

    // Ensure the floating button is the only trigger that opens the chat.
    if (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        goToChat();
      });
      // keyboard accessibility
      btn.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          goToChat();
        }
      });
    }

    // Nothing else should auto-open the chat. Bail out if chat UI missing.
    if (!modalEl || !messagesEl || !inputEl || !sendBtn) return;

    // Helpers
    function escapeHtml(s) {
      return (s + '').replace(/[&<>"']/g, function (m) {
        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
      });
    }

    function appendMessage(text, who = 'bot') {
      const wrapper = document.createElement('div');
      wrapper.className = 'poc-chat-msg ' + (who === 'user' ? 'user' : 'bot');

      if (who === 'user') {
        wrapper.innerHTML = `<div class="poc-chat-bubble">${escapeHtml(text)}</div><div class="avatar">U</div>`;
      } else {
        wrapper.innerHTML = `<div class="avatar">AI</div><div class="poc-chat-bubble">${escapeHtml(text)}</div>`;
      }

      messagesEl.appendChild(wrapper);
      messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function appendTypingIndicator() {
      const wrap = document.createElement('div');
      wrap.className = 'poc-chat-msg bot typing';
      wrap.innerHTML = `<div class="avatar">AI</div><div class="poc-chat-bubble"><div class="poc-chat-typing" aria-hidden="true"></div></div>`;
      messagesEl.appendChild(wrap);
      messagesEl.scrollTop = messagesEl.scrollHeight;
      return wrap;
    }

    function removeNode(node) {
      if (node && node.parentNode) node.parentNode.removeChild(node);
    }

    function setSendingState(isSending) {
      inputEl.disabled = isSending;
      sendBtn.disabled = isSending;
      if (!isSending) inputEl.focus();
    }

    // Send message to backend
    async function sendMessage(text) {
      if (!text || !text.trim()) return;
      const message = text.trim();

      // append user message
      appendMessage(message, 'user');
      inputEl.value = '';
      setSendingState(true);

      // show typing
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

        // remove typing indicator
        removeNode(typingNode);

        if (!res.ok) {
          appendMessage('Sorry, I could not reach the assistant. Try again later.', 'bot');
        } else {
          // chatai.php returns escaped text via htmlspecialchars, safe to display
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
      sendMessage(inputEl.value);
    });

    inputEl.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendBtn.click();
      }
    });

    // When modal opens first time, add a welcome message (do not auto-open modal)
    modalEl.addEventListener('shown.bs.modal', function () {
      if (messagesEl.children.length === 0) {
        appendMessage('Hi! I am Pack IT Assistant. How can I help you today?', 'bot');
      }
    });

    // Keep conversation on close by default. If you want to clear on close, uncomment:
    // modalEl.addEventListener('hidden.bs.modal', function () { messagesEl.innerHTML = ''; });

    // Expose goToChat globally for other scripts that might call it
    window.goToChat = goToChat;

  })();
</script>