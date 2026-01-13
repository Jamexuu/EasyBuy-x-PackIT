<?php
// api/db.php
// Database connection bootstrap for PackIT
//
// Usage:
//   require_once __DIR__ . '/db.php';
//   // $pdo will be available (PDO instance) or null if connection failed.
//
// Configure using environment variables or edit the defaults below:
//   DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
//
// Notes:
// - This file intentionally does not exit on connection failure because it's included by other scripts.
//   Callers should check if $pdo is a PDO instance before using it.

declare(strict_types=1);

// Load configuration from environment first (recommended) or fall back to defaults.
$DB_HOST = getenv('DB_HOST') !== false ? getenv('DB_HOST') : '127.0.0.1';
$DB_PORT = getenv('DB_PORT') !== false ? getenv('DB_PORT') : '3306';
$DB_NAME = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'packit';
$DB_USER = getenv('DB_USER') !== false ? getenv('DB_USER') : 'root';
$DB_PASS = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

// Character set
$DB_CHAR = 'utf8mb4';

// DSN
$dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset={$DB_CHAR}";

// PDO options: use exceptions, associative fetch, and native prepares
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = null;

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
    // Optional: set timezone or other session variables here if needed
    // $pdo->exec("SET time_zone = '+00:00'");
} catch (PDOException $e) {
    // Log the error for debugging but do not expose details to end users here.
    error_log('api/db.php - PDO connection failed: ' . $e->getMessage());
    // Leave $pdo as null so including code can handle the absence of a DB connection.
    $pdo = null;
}

// Make $pdo available to included files that check $GLOBALS['pdo']
$GLOBALS['pdo'] = $pdo;