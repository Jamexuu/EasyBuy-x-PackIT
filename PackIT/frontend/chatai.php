<?php
// AI BACKEND ONLY — NO HTML

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$input = trim($_POST['prompt'] ?? '');

if ($input === '') {
    echo 'Please enter a message.';
    exit;
}

$ch = curl_init('http://localhost:11434/api/chat');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode([
        'model' => 'qwen3:1.7b',
        'messages' => [
            [
                'role' => 'system',
                'content' =>
                    'You are POC, a friendly AI assistant for Pack IT, a packaging and delivery service.
                     You help customers with shipping, delivery updates, packaging tips, and general questions.
                     Be clear, friendly, and professional. Do NOT invent parcel data.'
            ],
            [
                'role' => 'user',
                'content' => $input
            ]
        ],
        'stream' => false
    ])
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
echo htmlspecialchars($data['message']['content'] ?? 'POC is unavailable right now.');
