<?php
// api/classes/chatBot.php

require_once __DIR__ . '/Database.php';

class ChatBot {
    private $db;
    private $llmEndpoint = 'http://localhost:11434/api/chat';
    private $model = 'qwen3:1.7b'; 

    // Configuration
    private $defaultCurrency = 'PHP';
    private $currencySymbols = [
        'USD' => '$', 'PHP' => '₱', 'EUR' => '€', 
        'GBP' => '£', 'JPY' => '¥'
    ];

    private $allowedKeywords = [
        'booking', 'bookings', 'status', 'track', 'tracking', 'eta', 'arrival', 'estimated', 
        'estimate', 'fare', 'payment', 'paid', 'unpaid', 'driver', 'vehicle', 'pickup', 
        'drop', 'delivery', 'booking id', 'order id', 'booking #', 'booking no', 'cancel', 
        'reschedule', 'price', 'cost', 'amount', 'where'
    ];

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Main entry point to process a user message
     */
    public function processMessage($userId, $sessionId, $rawPrompt) {
        $prompt = trim((string)$rawPrompt);
        if ($prompt === '') {
            return ['success' => false, 'error' => 'Please enter a message.'];
        }

        // 1. Check Keywords (Filter unrelated topics)
        if (!$this->isAllowedPrompt($prompt)) {
            $reply = "I can only answer questions related to PackIT bookings, delivery status, estimated arrival times, fares, payments, and tracking.";
            $this->logConversation($userId, $sessionId, $prompt, $reply);
            return ['success' => true, 'reply' => $reply];
        }

        // 2. Build Context (Booking info)
        $contextData = $this->getBookingContext($userId, $prompt);
        $systemPrompt = $this->buildSystemPrompt($contextData);

        // 3. Call Ollama
        $response = $this->callOllama($systemPrompt, $contextData, $prompt);
        
        if (!$response['success']) {
            return ['success' => false, 'error' => $response['error']];
        }

        $botReply = $this->cleanResponse($response['reply'], $contextData['currency_symbol']);

        // 4. Log to Database
        $this->logConversation($userId, $sessionId, $prompt, $botReply);

        return ['success' => true, 'reply' => $botReply];
    }

    /**
     * Fetch chat history
     */
    public function getHistory($userId, $sessionId) {
        if ($userId) {
            $sql = "SELECT id, prompt, response, created_at FROM chat_history WHERE user_id = ? ORDER BY id ASC LIMIT 100";
            $stmt = $this->db->executeQuery($sql, [(string)$userId]);
        } else {
            $sql = "SELECT id, prompt, response, created_at FROM chat_history WHERE session_id = ? ORDER BY id ASC LIMIT 100";
            $stmt = $this->db->executeQuery($sql, [$sessionId]);
        }
        return $this->db->fetch($stmt);
    }

    // --- Private Helper Methods ---

    private function isAllowedPrompt($text) {
        $tLower = mb_strtolower($text);
        foreach ($this->allowedKeywords as $k) {
            if (mb_strpos($tLower, $k) !== false) return true;
        }
        return false;
    }

    private function getBookingContext($userId, $prompt) {
        // Extract Booking ID from prompt (e.g., "status of booking 15")
        $bookingId = null;
        if (preg_match('/booking(?:\s*(?:id|no|#)?)\s*[:#-]?\s*(\d{1,10})/i', $prompt, $m)) {
            $bookingId = (int)$m[1];
        } elseif (preg_match('/\b#?(\d{3,10})\b/', $prompt, $m) && preg_match('/\bbooking\b/i', $prompt)) {
            $bookingId = (int)$m[1];
        }

        $contextText = "";
        $detectedCurrency = $this->defaultCurrency; 

        try {
            if ($bookingId) {
                // FIXED: Removed 'currency' and 'currency_code' from SELECT list
                $sql = "SELECT id, vehicle_type, tracking_status, total_amount, payment_status, created_at 
                        FROM bookings WHERE id = ? LIMIT 1";
                $stmt = $this->db->executeQuery($sql, [(string)$bookingId]);
                $rows = $this->db->fetch($stmt);
                
                if (!empty($rows)) {
                    $row = $rows[0];
                    // Append default currency for the AI context
                    $row['currency'] = $detectedCurrency; 
                    $contextText = "BookingContext: " . json_encode($row, JSON_UNESCAPED_UNICODE);
                }
            } elseif ($userId) {
                // FIXED: Removed 'currency' and 'currency_code' from SELECT list
                // Fetch recent 3 bookings for context
                $sql = "SELECT id, vehicle_type, tracking_status, total_amount, payment_status, created_at 
                        FROM bookings WHERE user_id = ? ORDER BY created_at DESC LIMIT 3";
                $stmt = $this->db->executeQuery($sql, [(string)$userId]);
                $rows = $this->db->fetch($stmt);
                
                if (!empty($rows)) {
                    // Add currency to each row for the AI
                    foreach ($rows as &$r) {
                        $r['currency'] = $detectedCurrency;
                    }
                    $summary = ['user_id' => (int)$userId, 'recent_bookings' => $rows];
                    $contextText = "UserBookings: " . json_encode($summary, JSON_UNESCAPED_UNICODE);
                }
            }
        } catch (Exception $e) {
            // Log error but continue so the chatbot doesn't crash completely
            error_log("ChatBot Context Error: " . $e->getMessage());
        }

        $symbol = $this->currencySymbols[$detectedCurrency] ?? '$';

        return [
            'text' => $contextText,
            'currency_code' => $detectedCurrency,
            'currency_symbol' => $symbol
        ];
    }

    private function buildSystemPrompt($contextData) {
        return <<<SYS
You are POC, a PackIT assistant. Answer ONLY about PackIT bookings, tracking, ETA, fares, payments and related system topics.
Rules:
- If the user is signed in, assume they mean their own bookings unless they explicitly ask about a different booking id.
- Use provided BookingContext or UserBookings data to answer directly.
- Display amounts using the currency code "{$contextData['currency_code']}" and symbol "{$contextData['currency_symbol']}".
- If multiple bookings exist and the user did not specify which one, ask a concise clarifying question.
- Keep answers concise (1-3 short sentences). Do not invent details.
SYS;
    }

    private function callOllama($systemPrompt, $contextData, $userPrompt) {
        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        
        if ($contextData['text']) {
            $messages[] = ['role' => 'system', 'content' => $contextData['text']];
        }

        $messages[] = ['role' => 'user', 'content' => $userPrompt];

        $ch = curl_init($this->llmEndpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode([
                'model' => $this->model,
                'messages' => $messages,
                'stream' => false
            ]),
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($curlErr || $httpCode !== 200) {
            return ['success' => false, 'error' => 'AI Service Unavailable'];
        }

        $data = json_decode($response, true);
        $reply = $data['message']['content'] ?? 'I could not process that request.';
        
        return ['success' => true, 'reply' => $reply];
    }

    private function cleanResponse($reply, $currencySymbol) {
        // Remove "thinking" blocks
        $reply = preg_replace('/\s*"thinking"\s*:\s*".*?"/s', '', $reply);
        
        // Fix currency formatting (e.g., $100 -> ₱100 if PHP)
        if ($currencySymbol !== '$') {
            $reply = preg_replace_callback('/\$\s?([0-9][0-9,\.]*)/', function($m) use ($currencySymbol) {
                return $currencySymbol . $m[1];
            }, $reply);
            $reply = preg_replace('/\bUSD\s+([0-9][0-9,\.]*)/', $currencySymbol . '$1', $reply);
        }
        return $reply;
    }

    private function logConversation($userId, $sessionId, $prompt, $response) {
        try {
            $sql = "INSERT INTO chat_history (session_id, user_id, prompt, response, created_at) VALUES (?, ?, ?, ?, NOW())";
            $params = [
                $sessionId,
                $userId ? (string)$userId : null,
                $prompt,
                $response
            ];
            $this->db->executeQuery($sql, $params);
        } catch (Exception $e) {
            error_log("Chat Log Error: " . $e->getMessage());
        }
    }
}
?>