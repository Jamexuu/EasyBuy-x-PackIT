<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

// Use the correct path, no login required
require_once __DIR__ . '/../frontend/booking/booking_state.php';

// Function to send JSON and exit
function send_json($arr, int $code = 200) {
    http_response_code($code);
    echo json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

// Only allow GET or POST
$method = $_SERVER['REQUEST_METHOD'];
if (!in_array($method, ['GET', 'POST'])) {
    send_json(['error' => 'Method Not Allowed'], 405);
}

// Get all vehicles/packages
$packages = get_packages();

// Flatten for API output as indexed list:
$results = [];
foreach ($packages as $package_key => $pkg) {
    $results[] = [
        'package_key'   => (string)$package_key,
        'vehicle_label' => $pkg['vehicle_label'] ?? ($pkg['label'] ?? ''),
        'amount'        => isset($pkg['amount']) ? (float)$pkg['amount'] : 0,
        'label'         => $pkg['label'] ?? '',
        'package_type'  => $pkg['package_type'] ?? '',
        'max_kg'        => $pkg['max_kg'] ?? null,
        'size' => [
            'length_m' => $pkg['size_length_m'] ?? null,
            'width_m'  => $pkg['size_width_m'] ?? null,
            'height_m' => $pkg['size_height_m'] ?? null,
        ],
    ];
}

send_json($results);