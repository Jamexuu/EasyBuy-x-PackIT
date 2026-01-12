<?php
// frontend/components/helpers.php
// Single shared helpers file — defines base URL and u() helper exactly once.
// Include with require_once __DIR__ . '/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Define a project base URL constant if not already set (adjust if your install path differs)
if (!defined('PACKIT_BASE_URL')) {
    define('PACKIT_BASE_URL', '/EasyBuy-x-PackIT/PackIT');
}

// Backwards-compatible $BASE_URL variable (optional)
if (!isset($BASE_URL)) {
    $BASE_URL = PACKIT_BASE_URL;
}

// URL helper, only declare if not exists
if (!function_exists('u')) {
    function u($path)
    {
        $base = rtrim(constant('PACKIT_BASE_URL'), '/');
        return $base . '/' . ltrim($path, '/');
    }
}
?>