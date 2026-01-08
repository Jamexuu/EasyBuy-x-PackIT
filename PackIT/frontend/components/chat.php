<style>
  /* --- Chat Button (Floating) --- */
  .poc-chat-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #facc15; /* Brand Yellow */
    color: #1f2937;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: transform 0.2s, box-shadow 0.2s;
    z-index: 1060; /* Higher than most elements */
  }

  .poc-chat-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  }

  /* Tooltip for the button */
  .poc-tooltip {
    position: absolute;
    right: 70px; /* To the left of the button */
    top: 50%;
    transform: translateY(-50%);
    background: #333;
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s;
  }

  .poc-chat-btn:hover .poc-tooltip {
    opacity: 1;
  }

  /* --- Chat Modal (Widget) --- */
  /* Remove default modal margins to allow custom positioning */
  .poc-chat-modal .modal-dialog {
    margin: 0;
    position: fixed;
    bottom: 90px; /* Above the button */
    right: 20px;
    width: 100%; /* Full width container */
    max-width: 400px; /* Max width for desktop */
    z-index: 1061;
  }
  
  /* Mobile Adjustments: Center and wider on small screens */
  @media (max-width: 576px) {
    .poc-chat-modal .modal-dialog {
        right: 50%;
        transform: translateX(50%) !important; /* Center horizontally */
        bottom: 85px;
        width: 92%; /* 92% of screen width */
        max-width: none;
    }
  }

  .poc-chat-window {
    height: 480px; /* Fixed height for the chat area */
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #fff;
    border-radius: 0.5rem; /* Bootstrap rounded */
  }

  /* Chat Messages Area */
  .poc-chat-messages {
    flex: 1 1 auto;
    padding: 1rem;
    overflow-y: auto;
    background: linear-gradient(180deg, #f8f9fa, #fff);
  }

  /* Input Area */
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
    padding: 0.6rem 0.8rem;
    border-radius: 1rem;
    font-size: 0.95rem;
    line-height: 1.4;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
  }

  /* User Message Styles */
  .poc-chat-msg.user {
    justify-content: flex-end;
  }
  .poc-chat-msg.user .poc-chat-bubble {
    background: #0d6efd; /* Bootstrap Primary */
    color: #fff;
    border-bottom-right-radius: 0.25rem;
  }

  /* Bot Message Styles */
  .poc-chat-msg.bot .poc-chat-bubble {
    background: #f1f3f5; /* Light Gray */
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

  @keyframes poc-blink {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 1; }
  }

  /* Input Controls */
  .poc-chat-controls textarea {
    resize: none;
    height: 42px; /* Single line height */
    font-size: 0.95rem;
  }
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
          <div class="poc-chat-messages" id="pocChatMessages" aria-live="polite">
            </div>

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
    
    // Updated Endpoint Path
    const chatEndpoint = "/EasyBuy-x-PackIT/PackIT/frontend/chatai.php";

    // --- Core Logic ---
    function goToChat() {
      if (!modalEl) {
        // Fallback redirection
        window.location.href = chatEndpoint; 
        return;
      }
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

    // --- Message Helpers ---
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

    // --- Send Logic ---
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

    // --- Event Listeners ---
    sendBtn.addEventListener('click', () => sendMessage(inputEl.value));

    inputEl.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendBtn.click();
      }
    });

    modalEl.addEventListener('shown.bs.modal', function () {
      inputEl.focus();
      if (messagesEl.children.length === 0) {
        appendMessage('Hi! I am Pack IT Assistant. How can I help you today?', 'bot');
      }
    });

    // Global Access
    window.goToChat = goToChat;
  })();
</script>