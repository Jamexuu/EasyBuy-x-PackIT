<?php
declare(strict_types=1);

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/classes/Database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

/**
 * Region-to-base mapping.
 */
function get_distance_fare_by_region(): array {
    return [
        "NCR" => 100,
        "NORTH" => 200,
        "SOUTH" => 200,
        "VISAYAS" => 300,
        "MINDANAO" => 500,
    ];
}

/**
 * Same-region -> 100
 * Cross-region -> use drop region mapping if available
 */
function compute_distance_fare(?string $pickupRegion, ?string $dropRegion): ?int {
    if (!$pickupRegion || !$dropRegion) return null;

    $pickup = strtoupper(trim($pickupRegion));
    $drop = strtoupper(trim($dropRegion));

    $table = get_distance_fare_by_region();

    // Same-region rule: any region to same region => 100
    if ($pickup === $drop) {
        return 100;
    }

    // For cross-region trips, use the dropRegion mapping if available
    return $table[$drop] ?? null;
}

// Optional query params to compute a single fare
$pickupParam = $_GET['pickupRegion'] ?? null;
$dropParam = $_GET['dropRegion'] ?? null;

try {
    // If both pickup and drop are provided, return a single fare result
    if ($pickupParam !== null || $dropParam !== null) {
        if (empty($pickupParam) || empty($dropParam)) {
            http_response_code(400);
            echo json_encode(['error' => 'Both pickupRegion and dropRegion are required when requesting a single fare']);
            exit();
        }

        $fare = compute_distance_fare($pickupParam, $dropParam);

        if ($fare === null) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Fare not found for the provided regions'
            ]);
            exit();
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'pickupRegion' => strtoupper(trim($pickupParam)),
                'dropRegion' => strtoupper(trim($dropParam)),
                'fare' => $fare
            ]
        ]);
        exit();
    }

    // Otherwise return the full fare rules and matrix
    $regions = get_distance_fare_by_region();

    // Build full fare matrix
    $fareMatrix = [];
    foreach ($regions as $pickup => $_) {
        $fareMatrix[$pickup] = [];
        foreach ($regions as $drop => $_) {
            $fareMatrix[$pickup][$drop] = compute_distance_fare($pickup, $drop);
        }
    }

    $response = [
        'success' => true,
        'data' => [
            'regions' => $regions,
            'rules' => [
                'same_region_amount' => 100,
                'cross_region_behavior' => 'use drop region mapping value'
            ],
            'fare_matrix' => $fareMatrix
        ]
    ];

    echo json_encode($response);
    exit();
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal Server Error',
        'message' => $e->getMessage()
    ]);
    exit();
}