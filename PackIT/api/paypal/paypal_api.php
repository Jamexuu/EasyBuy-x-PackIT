<?php
declare(strict_types=1);

require_once __DIR__ . "/../../frontend/booking/booking_state.php";
require_once __DIR__ . "/../../frontend/booking/fare_rules.php";
require_once __DIR__ . "/paypal_config.php";
require_once __DIR__ . "/../classes/Database.php";

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

function generateAccessToken(): string {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_BASE_URL . "/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ":" . PAYPAL_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }
    curl_close($ch);

    $data = json_decode($response, true);
    if (!isset($data['access_token'])) {
        throw new Exception("Failed to get Access Token: " . ($data['error_description'] ?? 'Unknown error'));
    }
    return $data['access_token'];
}

function createOrder($amount) {
    $token = generateAccessToken();

    $data = [
        "intent" => "CAPTURE",
        "purchase_units" => [[
            "amount" => [
                "currency_code" => "PHP",
                "value" => number_format((float)$amount, 2, '.', '')
            ]
        ]]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_BASE_URL . "/v2/checkout/orders");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $token
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

function captureOrder(string $orderId): array {
    $token = generateAccessToken();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_BASE_URL . "/v2/checkout/orders/$orderId/capture");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $token
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $decoded = json_decode($response, true);
    return is_array($decoded) ? $decoded : [];
}

function getLoggedInUserId(): ?int {
    if (!empty($_SESSION['user']['id'])) return (int)$_SESSION['user']['id'];
    if (!empty($_SESSION['user_id'])) return (int)$_SESSION['user_id'];
    return null;
}

function extractCaptureId(array $capture): ?string {
    $id = $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
    return (is_string($id) && $id !== '') ? $id : null;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

try {
    if ($action === 'create_order') {
        // Require login before booking (your requirement)
        $userId = getLoggedInUserId();
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['error' => 'Please log in before booking.']);
            exit;
        }

        $state = get_booking_state();

        $base = (float)($state["base_amount"] ?? 0);
        $pRegion = isset($state["pickup_region"]) ? (string)$state["pickup_region"] : null;
        $dRegion = isset($state["drop_region"]) ? (string)$state["drop_region"] : null;

        $dist = compute_distance_fare($pRegion, $dRegion);
        $door = get_door_to_door_amount((bool)($state["door_to_door"] ?? true));
        if ($dist === null) throw new Exception("Invalid Booking State: Distance fare not found.");

        $totalAmount = compute_total_fare((int)$base, $dist, $door);

        $order = createOrder($totalAmount);
        echo json_encode($order);
        exit;
    }

    if ($action === 'capture_order') {
        $orderId = (string)($input['orderID'] ?? '');
        if ($orderId === '') throw new Exception("Missing Order ID");

        // Require login before booking (your requirement)
        $userId = getLoggedInUserId();
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['error' => 'Please log in before booking.']);
            exit;
        }

        $capture = captureOrder($orderId);

        if (($capture['status'] ?? '') === 'COMPLETED') {
            $state = get_booking_state();

            // Validate state
            if (empty($state['pickup_address']) || empty($state['drop_address']) || empty($state['vehicle_label'])) {
                throw new Exception("Invalid Booking State: missing address/vehicle.");
            }

            $pickup = (array)$state['pickup_address'];
            $drop   = (array)$state['drop_address'];

            $pickupMunicipality = trim((string)($pickup['municipality'] ?? ''));
            $pickupProvince     = trim((string)($pickup['province'] ?? ''));
            $dropMunicipality   = trim((string)($drop['municipality'] ?? ''));
            $dropProvince       = trim((string)($drop['province'] ?? ''));

            if ($pickupMunicipality === '' || $pickupProvince === '' || $dropMunicipality === '' || $dropProvince === '') {
                throw new Exception("Invalid Booking State: pickup/drop municipality & province are required.");
            }

            // Amounts (recompute server-side)
            $baseAmount = (int)($state["base_amount"] ?? 0);
            $pRegion = isset($state["pickup_region"]) ? (string)$state["pickup_region"] : null;
            $dRegion = isset($state["drop_region"]) ? (string)$state["drop_region"] : null;

            $distanceAmount = compute_distance_fare($pRegion, $dRegion);
            $doorToDoorAmount = get_door_to_door_amount((bool)($state["door_to_door"] ?? true));
            if ($distanceAmount === null) throw new Exception("Invalid Booking State: Distance fare not found.");

            $totalAmount = compute_total_fare($baseAmount, $distanceAmount, $doorToDoorAmount);

            // bookings.vehicle_type now matches vehicle_label enum you altered
            $vehicleType = (string)$state['vehicle_label'];

            $db = new Database();

            // Insert booking
            $db->executeQuery(
                "INSERT INTO bookings
                (user_id, driver_id, pickup_house, pickup_barangay, pickup_municipality, pickup_province,
                 drop_house, drop_barangay, drop_municipality, drop_province,
                 vehicle_type, distance_km, base_amount, distance_amount, door_to_door_amount, total_amount,
                 payment_status, payment_method, tracking_status, created_at)
                VALUES
                (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, ?, ?, ?, ?, 'paid', 'paypal', 'pending', NOW())",
                [
                    $userId,
                    (string)($pickup['house'] ?? null),
                    (string)($pickup['barangay'] ?? null),
                    $pickupMunicipality,
                    $pickupProvince,
                    (string)($drop['house'] ?? null),
                    (string)($drop['barangay'] ?? null),
                    $dropMunicipality,
                    $dropProvince,
                    $vehicleType,
                    (string)$baseAmount,
                    (string)$distanceAmount,
                    (string)$doorToDoorAmount,
                    (string)$totalAmount,
                ]
            );

            $bookingId = (int)$db->lastInsertId();
            if ($bookingId <= 0) {
                throw new Exception("Failed to create booking record.");
            }

            // Insert payment
            $captureId = extractCaptureId($capture);

            $db->executeQuery(
                "INSERT INTO payments (booking_id, paypal_order_id, paypal_capture_id, amount, currency, status, created_at)
                 VALUES (?, ?, ?, ?, 'PHP', 'completed', NOW())",
                [
                    $bookingId,
                    $orderId,
                    $captureId,
                    (string)$totalAmount,
                ]
            );

            // Clear booking session only after DB insert success
            unset($_SESSION['booking']);

            // Return capture + bookingId for debugging (optional)
            $capture['_packit_booking_id'] = $bookingId;
        }

        echo json_encode($capture);
        exit;
    }

    throw new Exception("Invalid Action");
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}