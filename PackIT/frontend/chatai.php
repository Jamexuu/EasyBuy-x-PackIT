<?php
// frontend/chatai.php
// Chat endpoint tailored to PackIT DB.
// - POST 'prompt' (required), optional 'model' (default 'gemma2:2b')
// - Builds a strict system prompt that includes current user + recent bookings + payments + driver + smslogs summary
// - NEW: Includes Vehicle List (name, fare, dims) for pricing questions.
// - Enforces domain limits: assistant must only answer website-related questions (bookings, tracking, payments, fares, ETA, etc.)
// - Calls Ollama local API and aggregates NDJSON streaming replies
// - Persists prompt/response to chat_history (session_id + user_id)
// - Returns JSON { success, reply, http_code, saved }
//
// IMPORTANT: Ensure /api/db.php exposes a PDO instance in $pdo (or update the code accordingly).

declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

session_start();

$prompt = trim((string)($_POST['prompt'] ?? ''));
$model  = trim((string)($_POST['model']  ?? 'gemma2:2b'));

if ($prompt === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Empty prompt']);
    exit;
}

// Load DB if available (expects api/db.php to set $pdo as PDO)
$pdo = null;
$dbPath = __DIR__ . '/../api/db.php';
if (file_exists($dbPath)) {
    try {
        require_once $dbPath;
        // Accept $pdo from the included file or $GLOBALS['pdo']
        if (!isset($pdo) || !($pdo instanceof PDO)) {
            if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
                $pdo = $GLOBALS['pdo'];
            }
        }
    } catch (Throwable $e) {
        $pdo = null;
    }
}

// Build user context from session and DB
$userId = $_SESSION['user']['id'] ?? null;
$userContext = [];
$userContext[] = "This context was extracted from the PackIT database and session. Use it exactly as given.";

// Include basic profile if present
if ($userId) {
    $first = $_SESSION['user']['firstName'] ?? ($_SESSION['user']['first_name'] ?? null);
    $last  = $_SESSION['user']['lastName']  ?? ($_SESSION['user']['last_name'] ?? null);
    $email = $_SESSION['user']['email'] ?? null;
    $phone = $_SESSION['user']['phone'] ?? ($_SESSION['user']['contact_number'] ?? null);

    if ($first || $last) $userContext[] = "Name: " . trim(($first ?? '') . ' ' . ($last ?? ''));
    if ($email) $userContext[] = "Email: " . $email;
    if ($phone) $userContext[] = "Phone: " . $phone;
    $userContext[] = "User ID: " . (string)$userId;
} else {
    $userContext[] = "User: anonymous (no logged-in user). For booking-specific info, require the user to log in.";
}

// ---------------------------------------------------------
// NEW: Fetch Vehicle List for Context (Pricing/Specs)
// ---------------------------------------------------------
if ($pdo instanceof PDO) {
    try {
        // Query columns based on your vehicles table screenshot
        $vSql = "SELECT name, package_type, fare, max_kg, size_length_m, size_width_m, size_height_m FROM vehicles ORDER BY id ASC";
        $vStmt = $pdo->prepare($vSql);
        $vStmt->execute();
        $vehicles = $vStmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($vehicles)) {
            $userContext[] = "Official Vehicle Pricing & Specs:";
            foreach ($vehicles as $v) {
                // Formatting: "Motorcycle: Base Fare 100.00, Max 20kg (Envelope, Small Bags). Dims: 0.5x0.4x0.5m"
                $userContext[] = sprintf(
                    "- %s: Base Fare %s, Max %skg (%s). Dimensions: %sx%sx%s meters.",
                    $v['name'],
                    $v['fare'],
                    $v['max_kg'],
                    $v['package_type'],
                    $v['size_length_m'],
                    $v['size_width_m'],
                    $v['size_height_m']
                );
            }
        }
    } catch (Throwable $e) {
        // ignore vehicle fetch errors
    }
}
// ---------------------------------------------------------

// Fetch recent bookings for user (if DB available)
$bookingsSummary = [];
if ($pdo instanceof PDO && $userId) {
    try {
        $sql = "SELECT b.*, p.status AS payment_status, p.amount AS payment_amount, d.first_name AS driver_first, d.last_name AS driver_last, d.contact_number AS driver_phone
                FROM bookings b
                LEFT JOIN payments p ON p.booking_id = b.id
                LEFT JOIN drivers d ON d.id = b.driver_id
                WHERE b.user_id = :uid
                ORDER BY b.created_at DESC
                LIMIT 10";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $r) {
            $id = $r['id'] ?? null;
            $created = $r['created_at'] ?? null;
            $tracking = $r['tracking_status'] ?? ($r['tracking'] ?? 'unknown');
            $paymentStat = $r['payment_status'] ?? ($r['payment_status'] ?? 'unknown');
            $total = isset($r['total_amount']) ? (string)$r['total_amount'] : (isset($r['total']) ? (string)$r['total'] : null);

            // pickup / drop location assembly
            $pickupParts = [];
            if (!empty($r['pickup_house'])) $pickupParts[] = $r['pickup_house'];
            if (!empty($r['pickup_barangay'])) $pickupParts[] = $r['pickup_barangay'];
            if (!empty($r['pickup_municipality'])) $pickupParts[] = $r['pickup_municipality'];
            if (!empty($r['pickup_province'])) $pickupParts[] = $r['pickup_province'];
            $pickup = implode(', ', array_filter($pickupParts));

            $dropParts = [];
            if (!empty($r['drop_house'])) $dropParts[] = $r['drop_house'];
            if (!empty($r['drop_barangay'])) $dropParts[] = $r['drop_barangay'];
            if (!empty($r['drop_municipality'])) $dropParts[] = $r['drop_municipality'];
            if (!empty($r['drop_province'])) $dropParts[] = $r['drop_province'];
            $drop = implode(', ', array_filter($dropParts));

            $driver = null;
            if (!empty($r['driver_first']) || !empty($r['driver_last'])) {
                $driver = trim(($r['driver_first'] ?? '') . ' ' . ($r['driver_last'] ?? ''));
            }
            $driverPhone = $r['driver_phone'] ?? null;

            // fetch latest smslog events for the booking (3 latest)
            $smsEvents = [];
            try {
                $smsStmt = $pdo->prepare("SELECT Status, Message, CreatedAt FROM smslogs WHERE BookingId = :bid ORDER BY CreatedAt DESC LIMIT 5");
                $smsStmt->execute([':bid' => $id]);
                $smsRows = $smsStmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($smsRows as $s) {
                    $smsEvents[] = trim(($s['Status'] ?? '') . ': ' . ($s['Message'] ?? '') . ' @ ' . ($s['CreatedAt'] ?? ''));
                }
            } catch (Throwable $e) {
                // ignore sms fetch errors
            }

            $bookingsSummary[] = [
                'id' => $id,
                'created_at' => $created,
                'tracking_status' => $tracking,
                'payment_status' => $paymentStat,
                'total_amount' => $total,
                'pickup' => $pickup,
                'drop' => $drop,
                'vehicle_type' => $r['vehicle_type'] ?? null,
                'package_desc' => $r['package_desc'] ?? null,
                'driver' => $driver,
                'driver_phone' => $driverPhone,
                'sms_events' => $smsEvents,
            ];
        }
    } catch (Throwable $e) {
        // ignore DB errors; assistant will work with what it has
    }
}

// Add bookings summary to context (structured, human-readable)
if (!empty($bookingsSummary)) {
    $userContext[] = "Recent bookings (most recent first):";
    foreach ($bookingsSummary as $b) {
        $line = sprintf(
            "Booking #%s — status: %s; payment: %s; amount: %s; created: %s; pickup: %s; drop: %s; driver: %s (%s)",
            $b['id'] ?? 'N/A',
            $b['tracking_status'] ?? 'unknown',
            $b['payment_status'] ?? 'unknown',
            $b['total_amount'] ?? 'N/A',
            $b['created_at'] ?? 'N/A',
            $b['pickup'] ?? 'N/A',
            $b['drop'] ?? 'N/A',
            $b['driver'] ?? 'unassigned',
            $b['driver_phone'] ?? 'N/A'
        );
        $userContext[] = $line;
        if (!empty($b['sms_events'])) {
            $userContext[] = "Recent messages for booking #{$b['id']}:";
            foreach ($b['sms_events'] as $ev) $userContext[] = "- " . $ev;
        }
    }
} else {
    if ($userId) {
        $userContext[] = "No recent bookings found for this user.";
    } else {
        $userContext[] = "No user logged in; cannot show bookings.";
    }
}

// SYSTEM PROMPT — make assistant strict about domain and data usage
$systemPreamble = "You are Gemma (gemma2:2b), the PackIT assistant. Follow rules strictly:\n";
$systemPreamble .= "1) Use ONLY the user context supplied below to answer. Do NOT invent facts or guess details not in the context.\n";
$systemPreamble .= "2) You may answer ONLY about PackIT site purpose: bookings, tracking status, delivery status, pickup/drop addresses, fares, vehicles, payments, and booking-related messages. For personal account changes, direct the user to profile or support.\n";
$systemPreamble .= "3) If the user asks about anything outside PackIT (e.g., weather, general knowledge, politics, medical/legal advice), politely refuse and say: 'I can only help with PackIT bookings, tracking, fares, payments and site-related questions.'\n";
$systemPreamble .= "4) For bookings, prefer to reference booking ID (e.g. Booking #20) and only provide non-sensitive details (status, amount, pickup/drop, driver name/phone when available, recent SMS events). Do NOT provide payment tokens or internal IDs beyond booking id and user id.\n";
$systemPreamble .= "5) If not logged in and the user asks about their bookings, ask them to log in and provide instructions.\n";
$systemPreamble .= "6) Keep replies concise, factual and actionable. Offer next steps where relevant (e.g., how to request cancellation or contact support).\n\n";
$systemPreamble .= "User context:\n";
$systemPreamble .= implode("\n", $userContext) . "\n\n";

// Combined prompt sent to model
$combinedPrompt = $systemPreamble . "User: " . $prompt . "\nAssistant:";

// Ollama endpoint
$ollamaBase = 'http://127.0.0.1:11434';
$ollamaEndpoint = rtrim($ollamaBase, '/') . '/api/generate';

// Build payload
$payload = [
    'model' => $model,
    'prompt' => $combinedPrompt,
    // you can set temperature, max tokens, etc., depending on your Ollama setup
];

// Call Ollama
$ch = curl_init($ollamaEndpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$responseBody = curl_exec($ch);
$curlErr = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($responseBody === false || $curlErr) {
    http_response_code(502);
    echo json_encode(['success' => false, 'error' => 'Could not reach model server', 'details' => $curlErr]);
    exit;
}

// Parse NDJSON streaming lines and assemble final reply
$reply = '';
$decodedLines = [];
$lines = preg_split('/\r?\n/', trim($responseBody));
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '') continue;
    $obj = json_decode($line, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // not JSON; append raw chunk
        $decodedLines[] = $line;
        $reply .= $line;
        continue;
    }
    $decodedLines[] = $obj;
    if (isset($obj['response']) && is_string($obj['response'])) {
        $reply .= $obj['response'];
    } elseif (isset($obj['text']) && is_string($obj['text'])) {
        $reply .= $obj['text'];
    } elseif (isset($obj['result']) && is_string($obj['result'])) {
        $reply .= $obj['result'];
    } elseif (isset($obj['generations'][0]['text'])) {
        $reply .= $obj['generations'][0]['text'];
    }
    if (isset($obj['done']) && $obj['done']) {
        break;
    }
}

// fallback whole-body parse
if ($reply === '') {
    $whole = json_decode($responseBody, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($whole)) {
        if (isset($whole['response']) && is_string($whole['response'])) $reply = $whole['response'];
        elseif (isset($whole['text']) && is_string($whole['text'])) $reply = $whole['text'];
        elseif (isset($whole['result']) && is_string($whole['result'])) $reply = $whole['result'];
        elseif (isset($whole['generations'][0]['text'])) $reply = $whole['generations'][0]['text'];
        elseif (isset($whole['choices'][0]['message']['content'])) $reply = $whole['choices'][0]['message']['content'];
        elseif (isset($whole['choices'][0]['text'])) $reply = $whole['choices'][0]['text'];
        else $reply = json_encode($whole, JSON_UNESCAPED_UNICODE);
        $decodedLines[] = $whole;
    } else {
        $reply = $responseBody;
    }
}

$reply = preg_replace('/\s+$/u', '', $reply);

// Persist chat_history (session_id + user_id)
$saved = false;
if ($pdo instanceof PDO) {
    try {
        $ins = $pdo->prepare("INSERT INTO chat_history (session_id, user_id, prompt, response, created_at) VALUES (:sid, :uid, :p, :r, :t)");
        $ins->execute([
            ':sid' => session_id(),
            ':uid' => $userId ?: null,
            ':p'   => $prompt,
            ':r'   => $reply,
            ':t'   => date('Y-m-d H:i:s'),
        ]);
        $saved = true;
    } catch (Throwable $e) {
        error_log("chat_history insert failed: " . $e->getMessage());
    }
}

// Return to frontend
echo json_encode([
    'success' => true,
    'reply' => $reply,
    'http_code' => $httpCode,
    'saved' => $saved,
    'debug' => [
        'model' => $model,
        //'context' => $userContext, // comment out in production if you consider it sensitive
    ],
], JSON_UNESCAPED_UNICODE);
exit;
?>