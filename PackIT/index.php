<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pack IT</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* Floating Button */
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
  box-shadow: 0 10px 30px rgba(0,0,0,.25);
  z-index: 1000;
}

/* Chat Popup */
.poc-chatbox {
  position: fixed;
  bottom: 100px;
  right: 24px;
  width: 360px;
  height: 520px;
  background: #fffef5;
  border-radius: 16px;
  box-shadow: 0 20px 50px rgba(0,0,0,.35);
  display: none;
  flex-direction: column;
  z-index: 1000;
}

/* Header */
.poc-header {
  background: #facc15;
  padding: 14px;
  font-weight: bold;
  display: flex;
  justify-content: space-between;
}

/* Messages */
.poc-messages {
  flex: 1;
  padding: 15px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.poc-msg {
  padding: 10px 14px;
  border-radius: 14px;
  max-width: 80%;
  font-size: 14px;
}

.user { align-self: flex-end; background: #fde047; }
.bot { align-self: flex-start; background: #e5e7eb; }

/* Input */
.poc-input {
  display: flex;
  padding: 12px;
  border-top: 1px solid #ddd;
}

.poc-input textarea {
  flex: 1;
  resize: none;
  border-radius: 8px;
  border: 1px solid #ccc;
  padding: 8px;
}

.poc-input button {
  background: #facc15;
  border: none;
  margin-left: 8px;
  padding: 0 16px;
  border-radius: 8px;
}
</style>
</head>

<body style="background:#fffef5">

<?php include("frontend/components/navbar.php"); ?>

<main class="container py-5">
  <h1 class="fw-bold">📦 Pack IT</h1>
  <p class="lead">We package and deliver your parcels safely and on time.</p>
</main>

<?php include("frontend/components/footer.php"); ?>

<!-- CHAT POPUP -->
<div class="poc-chatbox" id="pocBox">
  <div class="poc-header">
    📦 POC Assistant
    <span style="cursor:pointer" onclick="toggleChat()">✖</span>
  </div>

  <div class="poc-messages" id="pocMessages">
    <div class="poc-msg bot">
      Hi! I'm <b>POC</b>. How can I help with your delivery today?
    </div>
  </div>

  <div class="poc-input">
    <textarea id="pocInput"
      placeholder="Ask about your parcel..."
      onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendPOC();}">
    </textarea>
    <button onclick="sendPOC()">Send</button>
  </div>
</div>

<!-- FLOATING BUTTON -->
<div class="poc-chat-btn" onclick="toggleChat()">💬</div>

<script>
const box = document.getElementById('pocBox');
const messages = document.getElementById('pocMessages');
const input = document.getElementById('pocInput');

function toggleChat() {
  box.style.display = box.style.display === 'flex' ? 'none' : 'flex';
}

function addMsg(role, text) {
  const div = document.createElement('div');
  div.className = 'poc-msg ' + role;
  div.textContent = text;
  messages.appendChild(div);
  messages.scrollTop = messages.scrollHeight;
}

async function sendPOC() {
  const text = input.value.trim();
  if (!text) return;

  addMsg('user', text);
  input.value = '';

  const typing = document.createElement('div');
  typing.className = 'poc-msg bot';
  typing.textContent = 'POC is typing...';
  messages.appendChild(typing);

  const res = await fetch('frontend/chatai.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'prompt=' + encodeURIComponent(text)
  });

  const reply = await res.text();
  typing.remove();
  addMsg('bot', reply);
}
</script>

</body>
</html>
