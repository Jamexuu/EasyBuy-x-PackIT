<style>
    /* --- INTEGRATED CHAT STYLES (From chat.php) --- */
    
    /* Desktop: Fixed Widget Position */
    .poc-chat-modal .modal-dialog {
      margin: 0;
      position: fixed;
      bottom: 20px;
      right: 20px; /* Aligned to right */
      width: 400px; /* Fixed width for desktop */
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
        border-radius: 1.5rem 1.5rem 0 0 !important; /* Round top corners only */
        border: none;
        box-shadow: 0 -10px 40px rgba(0,0,0,0.1);
        height: 80vh; /* Taller on mobile for better view */
        display: flex;
        flex-direction: column;
      }

      .poc-chat-window {
        height: 100% !important; /* Fill the modal content */
        border-radius: 0 !important;
      }
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
</style>