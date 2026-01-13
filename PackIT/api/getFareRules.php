<?php
declare(strict_types=1);

// CORS Headers - Allow cross-origin requests from EasyBuy
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// -------- Fare Logic --------
function get_distance_fare_by_region(): array {
    return [
        "NCR"      => 100,
        "NORTH"    => 200,
        "SOUTH"    => 200,
        "VISAYAS"  => 300,
        "MINDANAO" => 500,
    ];
}

function compute_distance_fare(?string $pickupRegion, ?string $dropRegion): ?int {
    if (!$pickupRegion || !$dropRegion) return null;
    $pickup = strtoupper(trim($pickupRegion));
    $drop   = strtoupper(trim($dropRegion));
    $table  = get_distance_fare_by_region();
    // Flat same-region rule
    if ($pickup === $drop) return 100;
    return $table[$drop] ?? null;
}

// ------------- Main API ---------------

$DEFAULT_PICKUP_REGION = "NCR"; // <-- EasyBuy Branch default

$pickupParam = $_GET['pickupRegion'] ?? null;
$dropParam   = $_GET['dropRegion'] ?? null;

try {
    // --- Single fare lookups ---
    if ($pickupParam !== null || $dropParam !== null) {
        // If only dropRegion is given, use the default pickup region!
        if (empty($pickupParam) && !empty($dropParam)) {
            $pickupParam = $DEFAULT_PICKUP_REGION;
        }
        if (empty($pickupParam) || empty($dropParam)) {
            http_response_code(400);
            echo json_encode(['error' => 'Both pickupRegion and dropRegion required or just dropRegion (uses EasyBuy default pickup branch)']);
            exit();
        }

        $fare = compute_distance_fare($pickupParam, $dropParam);

        if ($fare === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Fare not found for the provided regions']);
            exit();
        }

        echo json_encode([
            'pickupRegion' => strtoupper(trim($pickupParam)),
            'dropRegion'   => strtoupper(trim($dropParam)),
            'fare'         => $fare,
            'default_pickup_region' => $DEFAULT_PICKUP_REGION
        ]);
        exit();
    }

    // --- Return full matrix/rules/default-pickup for config/UX ---
    $regions = get_distance_fare_by_region();
    $fareMatrix = [];
    foreach ($regions as $pickup => $_) {
        $fareMatrix[$pickup] = [];
        foreach ($regions as $drop => $_) {
            $fareMatrix[$pickup][$drop] = compute_distance_fare($pickup, $drop);
        }
    }
    echo json_encode([
        'default_pickup_region' => $DEFAULT_PICKUP_REGION,
        'regions' => $regions,
        'rules' => [
            'same_region_amount' => 100,
            'cross_region_behavior' => 'use drop region mapping value',
            'EasyBuy_Branch' => 'If no pickupRegion is provided, pickup will default to "' . $DEFAULT_PICKUP_REGION . '" for EasyBuy orders.'
        ],
        'fare_matrix' => $fareMatrix
    ]);
    exit();
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal Server Error',
        'message' => $e->getMessage()
    ]);
    exit();
}