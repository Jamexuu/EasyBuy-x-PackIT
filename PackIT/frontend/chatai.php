<?php
// frontend/chatai.php
// Chat endpoint: receives 'prompt' via POST; auto-uses logged-in user's bookings as context;
// forwards to local LLM (Ollama), saves prompt+response to chat_history (if DB available), returns JSON { success, reply }.

declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', '0');
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (session_status() === PHP_SESSION_NONE) session_start();

// CONFIG: default currency (change if your app's primary currency differs)
$DEFAULT_CURRENCY = 'PHP'; // "PHP" for Philippine Peso by default

// Mapping from ISO currency code to common symbol
$CURRENCY_SYMBOLS = [
    'USD' => '$',
    'PHP' => '₱',
    'EUR' => '€',
    'GBP' => '£',
    'JPY' => '¥',
    // add others as needed
];

// Load DB adapter if present (api/db.php should set $pdo if available)
$pdo = null;
$dbPath = __DIR__ . '/../api/db.php';
if (file_exists($dbPath)) {
    try {
        require_once $dbPath; // expected to set $pdo (PDO|null)
        if (!isset($pdo) || !($pdo instanceof PDO)) {
            $pdo = $pdo ?? null;
        }
    } catch (Throwable $e) {
        error_log("Could not require api/db.php: " . $e->getMessage());
        $pdo = null;
    }
}

// Helpers
function json_err($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = $_POST['prompt'] ?? '';
$prompt = trim((string)$raw);
if ($prompt === '') {
    json_err('Please enter a message.', 400);
}
$max_len = 4000;
if (mb_strlen($prompt) > $max_len) $prompt = mb_substr($prompt, 0, $max_len);

$session_id = session_id();
$user_id = $_SESSION['user']['id'] ?? null;

// Allowed keywords for routing
$allowed_keywords = [
    'booking', 'bookings', 'status', 'track', 'tracking', 'eta', 'arrival', 'estimated', 'estimate',
    'fare', 'payment', 'paid', 'unpaid', 'driver', 'vehicle', 'pickup', 'drop', 'delivery',
    'booking id', 'order id', 'booking #', 'booking no', 'cancel', 'reschedule', 'price', 'cost', 'amount',
    'where', 'where is', 'where are'
];

function is_allowed_prompt(string $t, array $keywords): bool {
    $tLower = mb_strtolower($t);
    foreach ($keywords as $k) {
        if (mb_strpos($tLower, $k) !== false) return true;
    }
    return false;
}

function extract_booking_id(string $t): ?int {
    if (preg_match('/booking(?:\s*(?:id|no|#)?)\s*[:#-]?\s*(\d{1,10})/i', $t, $m)) {
        return (int)$m[1];
    }
    if (preg_match('/\b#?(\d{3,10})\b/', $t, $m) && preg_match('/\bbooking\b/i', $t)) {
        return (int)$m[1];
    }
    return null;
}

function save_chat_safe($pdo, $session_id, $user_id, $prompt, $response) {
    if ($pdo === null) return false;
    try {
        $stmt = $pdo->prepare("INSERT INTO chat_history (session_id, user_id, prompt, response) VALUES (:session_id, :user_id, :prompt, :response)");
        $stmt->execute([
            ':session_id' => $session_id,
            ':user_id' => $user_id,
            ':prompt' => $prompt,
            ':response' => $response
        ]);
        return true;
    } catch (Throwable $e) {
        error_log("Chat save error: " . $e->getMessage());
        return false;
    }
}

// Early refusal if prompt is unrelated and no login context that suggests otherwise
if (!is_allowed_prompt($prompt, $allowed_keywords)) {
    $reply = "I can only answer questions related to PackIT bookings, delivery status, estimated arrival times, fares, payments, and tracking. I cannot assist with unrelated topics.";
    save_chat_safe($pdo, $session_id, $user_id, $prompt, $reply);
    echo json_encode(['success' => true, 'reply' => $reply], JSON_UNESCAPED_UNICODE);
    exit;
}

// Build booking context:
// - If a booking id is present in the prompt, fetch that booking (if DB available).
// - Else if user is logged in ($user_id) and DB available, fetch the user's recent bookings (limit 3) and include a safe summary.
// Try to include a currency field (if available in DB). Use COALESCE so it works with either 'currency' or 'currency_code' column names.
$booking_id = extract_booking_id($prompt);
$booking_context_text = '';
$detected_currency = null; // e.g., "PHP" or "USD"
$detected_currency_symbol = null;

if ($pdo !== null) {
    try {
        if ($booking_id !== null) {
            $stmt = $pdo->prepare("
                SELECT id, vehicle_type, tracking_status, total_amount, payment_status, created_at,
                       COALESCE(currency, currency_code, '') AS currency
                FROM bookings
                WHERE id = :id
                LIMIT 1
            ");
            $stmt->execute([':id' => $booking_id]);
            $booking_row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($booking_row) {
                $detected_currency = strtoupper(trim((string)($booking_row['currency'] ?? '')));
                if ($detected_currency === '') $detected_currency = null;
                $booking_context_text = "BookingContext: " . json_encode($booking_row, JSON_UNESCAPED_UNICODE);
            }
        } elseif ($user_id !== null) {
            // fetch user's most recent 3 bookings (safe fields only)
            $stmt = $pdo->prepare("
                SELECT id, vehicle_type, tracking_status, total_amount, payment_status, created_at,
                       COALESCE(currency, currency_code, '') AS currency
                FROM bookings
                WHERE user_id = :uid
                ORDER BY created_at DESC
                LIMIT 3
            ");
            $stmt->execute([':uid' => $user_id]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                // If any row has a currency, prefer the most recent non-empty currency
                foreach ($rows as $r) {
                    $c = strtoupper(trim((string)($r['currency'] ?? '')));
                    if ($c !== '') { $detected_currency = $c; break; }
                }
                $summary = ['user_id' => (int)$user_id, 'recent_bookings' => $rows];
                $booking_context_text = "UserBookings: " . json_encode($summary, JSON_UNESCAPED_UNICODE);
            }
        }
    } catch (Throwable $e) {
        error_log("DB error fetching booking(s): " . $e->getMessage());
        // continue without DB context
    }
}

// Determine currency code and symbol to send as extra system context
if (empty($detected_currency)) {
    $detected_currency = $DEFAULT_CURRENCY;
}
$detected_currency = strtoupper($detected_currency);
$detected_currency_symbol = $CURRENCY_SYMBOLS[$detected_currency] ?? $CURRENCY_SYMBOLS[$DEFAULT_CURRENCY] ?? '$';

// Improved system prompt: assume user's logged-in context when possible and ask clarifying only if ambiguous
$system_prompt = <<<SYS
You are POC, a PackIT assistant. Answer ONLY about PackIT bookings, tracking, ETA, fares, payments and related system topics.
Rules:
- If the user is signed in, assume they mean their own bookings unless they explicitly ask about a different booking id.
- Use any provided BookingContext or UserBookings data to answer directly and cite the booking id when relevant.
- If the booking context includes 'total_amount' and 'currency', use those exact values when asked about cost/payment.
- Use the currency code and prefer the appropriate currency symbol when presenting amounts.
  - For example, if the BookingContext says currency = "PHP" you MUST display amounts using the Philippine peso symbol "₱" (e.g., "₱350.00") not "$350.00".
  - If currency = "USD", use "$". If unknown, use the currency code and symbol provided in the context.
- If multiple bookings exist and the user did not specify which one, ask a concise clarifying question: e.g., "Which booking do you mean? I see bookings 15 (pending) and 12 (delivered)."
- If no fare information is available for a booking, respond: "I don't have fare information for that booking. Please check your dashboard or provide booking details."
- Keep answers concise (1-3 short sentences). Do not invent details. Do not expose personal data.
SYS;

// Build messages for LLM
$messages = [
    ['role' => 'system', 'content' => $system_prompt],
];
if ($booking_context_text) {
    // Add explicit currency metadata as its own system content to make it easy for the model to use.
    $messages[] = ['role' => 'system', 'content' => $booking_context_text];
    $messages[] = ['role' => 'system', 'content' => "BookingCurrency: {\"code\":\"{$detected_currency}\",\"symbol\":\"{$detected_currency_symbol}\"}"];
}
$messages[] = ['role' => 'user', 'content' => $prompt];

// Call local Ollama API
$llm_endpoint = 'http://localhost:11434/api/chat';
$payload = [
    'model' => 'qwen3:1.7b',
    'messages' => $messages,
    'stream' => false
];

$ch = curl_init($llm_endpoint);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_TIMEOUT => 60,
    CURLOPT_CONNECTTIMEOUT => 10,
]);

$response = curl_exec($ch);
$curlErr = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $curlErr) {
    $errMsg = $curlErr ?: "HTTP code {$httpCode}";
    error_log("LLM request error: " . $errMsg);
    $reply = "POC is currently unavailable. Please try again later.";
    save_chat_safe($pdo, $session_id, $user_id, $prompt, $reply);
    echo json_encode(['success' => false, 'error' => 'LLM request failed: ' . $errMsg, 'reply' => $reply], JSON_UNESCAPED_UNICODE);
    exit;
}

// Parse JSON response (Ollama common shape)
$data = json_decode($response, true);
$reply = null;
if (is_array($data)) {
    if (!empty($data['message']['content'])) {
        $reply = (string)$data['message']['content'];
    } elseif (!empty($data['choices'][0]['message']['content'])) {
        $reply = (string)$data['choices'][0]['message']['content'];
    } elseif (!empty($data['choices'][0]['content'])) {
        $reply = (string)$data['choices'][0]['content'];
    } elseif (!empty($data['output'])) {
        $reply = is_string($data['output']) ? $data['output'] : json_encode($data['output'], JSON_UNESCAPED_UNICODE);
    } elseif (!empty($data['choices'][0]['text'])) {
        $reply = (string)$data['choices'][0]['text'];
    } else {
        $reply = substr(json_encode($data, JSON_UNESCAPED_UNICODE), 0, 2000);
    }
} else {
    $reply = (string)$response;
}

// Defensive cleanup: remove long debugging text if model included it
$reply = preg_replace('/\s*"thinking"\s*:\s*".*?"/s', '', $reply);

// Post-process currency symbol usage:
// If the detected currency symbol is not '$', replace instances where the model used "$" followed by an amount with the correct symbol.
// This is a safety-net so we don't display "$" for PHP amounts.
if (isset($detected_currency_symbol) && $detected_currency_symbol !== '$') {
    // Replace "$123", "$ 123", "$1,234.00" patterns
    $reply = preg_replace_callback('/\$\s?([0-9][0-9,\.]*)/', function($m) use ($detected_currency_symbol) {
        return $detected_currency_symbol . $m[1];
    }, $reply);
    // Also replace common "USD" markers next to amounts if model wrote "USD 123.45" (optional)
    $reply = preg_replace('/\bUSD\s+([0-9][0-9,\.]*)/', $detected_currency_symbol . '$1', $reply);
}

// Save chat
save_chat_safe($pdo, $session_id, $user_id, $prompt, $reply);

// Return reply
echo json_encode(['success' => true, 'reply' => $reply], JSON_UNESCAPED_UNICODE);
exit;
?>