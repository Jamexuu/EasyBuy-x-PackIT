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
    if (curl_errno($ch)) throw new Exception(curl_error($ch));
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
        $userId = getLoggedInUserId();
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['error' => 'Please log in before booking.']);
            exit;
        }

        $state = get_booking_state();

        $base = (int)($state["base_amount"] ?? 0);
        $pRegion = isset($state["pickup_region"]) ? (string)$state["pickup_region"] : null;
        $dRegion = isset($state["drop_region"]) ? (string)$state["drop_region"] : null;

        $dist = compute_distance_fare($pRegion, $dRegion);
        if ($dist === null) throw new Exception("Invalid Booking State: Distance fare not found.");

        $totalAmount = compute_total_fare($base, $dist, 0);

        $order = createOrder($totalAmount);
        echo json_encode($order);
        exit;
    }

    if ($action === 'capture_order') {
        $orderId = (string)($input['orderID'] ?? '');
        if ($orderId === '') throw new Exception("Missing Order ID");

        $userId = getLoggedInUserId();
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['error' => 'Please log in before booking.']);
            exit;
        }

        $capture = captureOrder($orderId);

        if (($capture['status'] ?? '') === 'COMPLETED') {
            $state = get_booking_state();

            if (empty($state['pickup_address']) || empty($state['drop_address']) || empty($state['vehicle_label'])) {
                throw new Exception("Invalid Booking State: missing address/vehicle.");
            }

            // Required: user package desc + quantity
            $packageDesc = trim((string)($state['package_desc'] ?? ''));
            $packageQty  = (int)($state['package_quantity'] ?? 0);
            if ($packageDesc === '' || $packageQty < 1) {
                throw new Exception("Invalid Booking State: missing package description/quantity.");
            }

            // Required: contacts
            $pickupContactName = trim((string)($state['pickup_contact_name'] ?? ''));
            $pickupContactNum  = trim((string)($state['pickup_contact_number'] ?? ''));
            $dropContactName   = trim((string)($state['drop_contact_name'] ?? ''));
            $dropContactNum    = trim((string)($state['drop_contact_number'] ?? ''));
            if ($pickupContactName === '' || $pickupContactNum === '' || $dropContactName === '' || $dropContactNum === '') {
                throw new Exception("Invalid Booking State: missing pickup/drop contact info.");
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
            if ($distanceAmount === null) throw new Exception("Invalid Booking State: Distance fare not found.");

            $doorToDoorAmount = 0;
            $totalAmount = compute_total_fare($baseAmount, $distanceAmount, 0);

            $vehicleType = (string)$state['vehicle_label'];
            $packageType = (string)($state['package_type'] ?? '');
            $maxKg       = (int)($state['max_kg'] ?? 0);
            $sizeL       = (float)($state['size_length_m'] ?? 0);
            $sizeW       = (float)($state['size_width_m'] ?? 0);
            $sizeH       = (float)($state['size_height_m'] ?? 0);

            $db = new Database();

            $db->executeQuery(
                "INSERT INTO bookings
                (user_id, driver_id,
                 pickup_contact_name, pickup_contact_number,
                 pickup_house, pickup_barangay, pickup_municipality, pickup_province,
                 drop_house, drop_barangay, drop_municipality, drop_province,
                 drop_contact_name, drop_contact_number,
                 vehicle_type, package_type, package_desc, package_quantity,
                 max_kg, size_length_m, size_width_m, size_height_m,
                 distance_km, base_amount, distance_amount, door_to_door_amount, total_amount,
                 payment_status, payment_method, tracking_status, created_at)
                VALUES
                (?, NULL,
                 ?, ?,
                 ?, ?, ?, ?,
                 ?, ?, ?, ?,
                 ?, ?,
                 ?, ?, ?, ?,
                 ?, ?, ?, ?,
                 NULL, ?, ?, ?, ?,
                 'paid', 'paypal', 'pending', NOW())",
                [
                    $userId,
                    $pickupContactName,
                    $pickupContactNum,
                    (string)($pickup['house'] ?? null),
                    (string)($pickup['barangay'] ?? null),
                    $pickupMunicipality,
                    $pickupProvince,
                    (string)($drop['house'] ?? null),
                    (string)($drop['barangay'] ?? null),
                    $dropMunicipality,
                    $dropProvince,
                    $dropContactName,
                    $dropContactNum,
                    $vehicleType,
                    $packageType,
                    $packageDesc,
                    (string)$packageQty,
                    (string)$maxKg,
                    (string)$sizeL,
                    (string)$sizeW,
                    (string)$sizeH,
                    (string)$baseAmount,
                    (string)$distanceAmount,
                    (string)$doorToDoorAmount,
                    (string)$totalAmount,
                ]
            );

            $bookingId = (int)$db->lastInsertId();
            if ($bookingId <= 0) throw new Exception("Failed to create booking record.");

            $captureId = extractCaptureId($capture);

            $db->executeQuery(
                "INSERT INTO payments (booking_id, paypal_order_id, paypal_capture_id, amount, currency, status, created_at)
                 VALUES (?, ?, ?, ?, 'PHP', 'completed', NOW())",
                [$bookingId, $orderId, $captureId, (string)$totalAmount]
            );

            unset($_SESSION['booking']);
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