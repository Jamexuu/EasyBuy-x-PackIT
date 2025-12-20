<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['prompt'] ?? '';

    $ch = curl_init('http://localhost:11434/api/chat');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    $data = [
        'model' => 'qwen3:1.7b',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are POC, a friendly AI assistant for Pack IT, a packaging and delivery service. Help users with shipping, parcels, delivery status, packaging tips, and general assistance.'
            ],
            [
                'role' => 'user',
                'content' => $input
            ]
        ],
        'stream' => false
    ];

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($response, true);
    echo htmlspecialchars($json['message']['content'] ?? 'No reply.');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>POC – Pack IT Assistant</title>

<style>
body {
  margin: 0;
  font-family: Inter, sans-serif;
  background: #fffef5;
  display: flex;
  flex-direction: column;
  height: 100vh;
}

header {
  background: #facc15;
  padding: 16px;
  font-weight: bold;
  font-size: 18px;
  text-align: center;
  color: #1f2937;
}

#chatbox {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.message {
  max-width: 70%;
  padding: 12px 16px;
  border-radius: 16px;
  white-space: pre-wrap;
}

.user {
  align-self: flex-end;
  background: #fde047;
}

.bot {
  align-self: flex-start;
  background: #e5e7eb;
}

#inputArea {
  display: flex;
  gap: 10px;
  padding: 15px;
  border-top: 1px solid #ddd;
  background: white;
}

#prompt {
  flex: 1;
  resize: none;
  padding: 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
}

button {
  background: #facc15;
  border: none;
  padding: 0 22px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
}
</style>
</head>

<body>

<header>📦 POC – Pack IT Assistant</header>

<div id="chatbox">
  <div class="message bot">
    Hi! I’m <b>POC</b> 👋<br>
    I can help you with packaging, delivery, and shipping questions.
  </div>
</div>

<div id="inputArea">
  <textarea id="prompt" placeholder="Ask POC something..."
    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage();}"></textarea>
  <button onclick="sendMessage()">Send</button>
</div>

<script>
const chatbox = document.getElementById('chatbox');
const input = document.getElementById('prompt');

function addMessage(role, text) {
  const div = document.createElement('div');
  div.className = 'message ' + role;
  div.textContent = text;
  chatbox.appendChild(div);
  chatbox.scrollTop = chatbox.scrollHeight;
}

async function sendMessage() {
  const text = input.value.trim();
  if (!text) return;

  addMessage('user', text);
  input.value = '';

  const typing = document.createElement('div');
  typing.className = 'message bot';
  typing.textContent = 'POC is typing...';
  chatbox.appendChild(typing);

  try {
    const res = await fetch('chatai.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'prompt=' + encodeURIComponent(text)
    });

    const reply = await res.text();
    typing.remove();
    addMessage('bot', reply);
  } catch {
    typing.remove();
    addMessage('bot', '⚠️ Unable to reach POC right now.');
  }
}
</script>

</body>
</html>
