<?php

// EasyBuy-style JSON response
header('Content-Type: application/json; charset=utf-8');

// Optional but helpful if EasyBuy frontend calls PackIT API in browser
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Preflight (browser)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}