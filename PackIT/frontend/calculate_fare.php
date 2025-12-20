<?php
// calculate_fare.php

if (isset($_GET['p_lat'], $_GET['p_lng'], $_GET['d_lat'], $_GET['d_lng'], $_GET['vehicle'])) {
    
    // 1. Get Inputs
    $pickup = $_GET['p_lng'] . ',' . $_GET['p_lat'];
    $dropoff = $_GET['d_lng'] . ',' . $_GET['d_lat'];
    $vehicleType = $_GET['vehicle'];

    // 2. Define Pricing Rules (No Database, just JSON/Array logic)
    $pricingRules = [
        'Motorcycle' => ['base' => 50, 'per_km' => 10], // Cheaper per km
        'Car'        => ['base' => 150, 'per_km' => 25] // Expensive start & per km
    ];

    // Default to Car if unknown
    $rates = isset($pricingRules[$vehicleType]) ? $pricingRules[$vehicleType] : $pricingRules['Car'];

    // 3. Call OSRM API for Distance
    $api_url = "http://router.project-osrm.org/route/v1/driving/$pickup;$dropoff?overview=false";
    
    // Use cURL to fetch
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);

    $data = json_decode($response, true);

    if (isset($data['routes'][0])) {
        // Distance comes in meters, convert to KM
        $distance_meters = $data['routes'][0]['distance'];
        $distance_km = $distance_meters / 1000;

        // 4. Calculate Final Price
        // Formula: Base Fare + (Distance * Per KM Price)
        $total_price = $rates['base'] + ($distance_km * $rates['per_km']);

        echo json_encode([
            'status' => 'success',
            'distance_km' => round($distance_km, 2),
            'price' => round($total_price, 2),
            'currency' => 'PHP'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Route not found']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
}
?>