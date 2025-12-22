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
        z-index: 1000;
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
</style>
<div class="poc-chat-btn" onclick="goToChat()">
    💬
    <div class="poc-tooltip">Chat with POC</div>
</div>

<script>
    function goToChat() {
        window.location.href = "../frontend/chatai.php";
    }
</script>