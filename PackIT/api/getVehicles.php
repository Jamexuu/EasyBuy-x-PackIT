<?php

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/classes/Vehicle.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

$vehicle = new Vehicle();
$vehicles = $vehicle->getAllVehicles();

echo json_encode([
    'success' => true,
    'data' => $vehicles
]);