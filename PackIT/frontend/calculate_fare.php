<?php
// calculate_fare.php (distance-based using OSRM) with:
// - included_km (first X km included in base fare)
// - minimum fare
// - rounding up to nearest 5 pesos

header('Content-Type: application/json; charset=utf-8');

function respond($arr, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode($arr);
    exit;
}

$required = ['p_lat','p_lng','d_lat','d_lng','vehicle'];
foreach ($required as $k) {
    if (!isset($_GET[$k]) || $_GET[$k] === '') {
        respond(['status' => 'error', 'message' => "Missing data: $k"], 400);
    }
}

$p_lat = filter_var($_GET['p_lat'], FILTER_VALIDATE_FLOAT);
$p_lng = filter_var($_GET['p_lng'], FILTER_VALIDATE_FLOAT);
$d_lat = filter_var($_GET['d_lat'], FILTER_VALIDATE_FLOAT);
$d_lng = filter_var($_GET['d_lng'], FILTER_VALIDATE_FLOAT);

if ($p_lat === false || $p_lng === false || $d_lat === false || $d_lng === false) {
    respond(['status' => 'error', 'message' => 'Invalid coordinates'], 400);
}

// Normalize vehicle
$vehicleRaw = trim((string)$_GET['vehicle']);
$v = strtolower($vehicleRaw);

if ($v === 'motorcycle' || $v === 'motorbike' || $v === 'bike') {
    $vehicleKey = 'Motorcycle';
} elseif ($v === 'car' || $v === 'sedan' || $v === 'sedan / car') {
    $vehicleKey = 'Car';
} else {
    $vehicleKey = 'Car';
}

/**
 * Pricing model:
 * total = base + max(0, distance_km - included_km) * per_km
 * then apply minimum fare and rounding
 *
 * Adjust numbers here to match your business rules.
 */
$pricingRules = [
    'Motorcycle' => [
        'base' => 70,         // base fare
        'included_km' => 3.0, // first 3km included in base
        'per_km' => 12,       // charge per km AFTER included_km
        'min_fare' => 70,     // absolute minimum
        'round_to' => 5       // round UP to nearest 5 pesos
    ],
    'Car' => [
        'base' => 170,
        'included_km' => 3.0,
        'per_km' => 25,
        'min_fare' => 170,
        'round_to' => 5
    ]
];

$rates = $pricingRules[$vehicleKey];

// OSRM requires lng,lat
$pickup = $p_lng . ',' . $p_lat;
$dropoff = $d_lng . ',' . $d_lat;

// Use HTTPS OSRM public server
$api_url = "https://router.project-osrm.org/route/v1/driving/$pickup;$dropoff?overview=false&alternatives=false&steps=false";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_USERAGENT => 'PackIT/1.0'
]);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($response === false) {
    respond(['status' => 'error', 'message' => 'OSRM request failed', 'details' => $err], 502);
}

$data = json_decode($response, true);
if (!is_array($data) || ($data['code'] ?? '') !== 'Ok' || empty($data['routes'][0]['distance'])) {
    respond([
        'status' => 'error',
        'message' => 'Route not found',
        'osrm_code' => $data['code'] ?? null,
        'osrm_message' => $data['message'] ?? null
    ], 404);
}

$distance_m = (float)$data['routes'][0]['distance'];
$distance_km = $distance_m / 1000.0;

if ($distance_km <= 0) {
    respond(['status' => 'error', 'message' => 'Invalid route distance'], 502);
}

$billable_km = max(0.0, $distance_km - (float)$rates['included_km']);

$total = (float)$rates['base'] + ($billable_km * (float)$rates['per_km']);
$total = max($total, (float)$rates['min_fare']);

// round UP to nearest N pesos
$roundTo = (float)$rates['round_to'];
if ($roundTo > 0) {
    $total = ceil($total / $roundTo) * $roundTo;
}

respond([
    'status' => 'success',
    'vehicle' => $vehicleKey,
    'distance_km' => round($distance_km, 2),
    'billable_km' => round($billable_km, 2),
    'price' => round($total, 2),
    'currency' => 'PHP'
]);