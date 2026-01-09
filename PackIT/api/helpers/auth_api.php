<?php

require_once __DIR__ . '/../classes/Auth.php';

/**
 * Start session + require logged-in user, but respond with JSON (no redirect).
 */
function require_api_auth() {
    Auth::start();

    if (!Auth::isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }

    return Auth::getUser();
}

function is_admin_or_driver($user) {
    return ($user['role'] === 'admin' || $user['role'] === 'driver');
}