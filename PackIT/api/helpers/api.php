<?php

function api_init() {
    header('Content-Type: application/json; charset=utf-8');

    // Allow EasyBuy (browser) to call PackIT endpoints
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    // Preflight request (browser sends OPTIONS first)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit();
    }
}

function read_json() {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];

    $data = json_decode($raw, true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
        exit();
    }
    return $data;
}

function method_not_allowed($allowed = '') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed', 'allowed' => $allowed]);
    exit();
}