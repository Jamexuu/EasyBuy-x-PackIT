<?php
// api/db.php
// Database adapter: creates $pdo (PDO) and a legacy $db (mysqli) if desired.
// Place this file at project-root/api/db.php

declare(strict_types=1);

// === Configure these values to match your environment ===
$DB_HOST = '127.0.0.1';
$DB_NAME = 'packit';
$DB_USER = 'root';
$DB_PASS = ''; // set your DB password
$DB_PORT = 3306;
$DB_CHARSET = 'utf8mb4';
// ========================================================

$pdo = null;
$db = null;

try {
    $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset={$DB_CHARSET}";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Log error to PHP error log. Do NOT echo credentials or DB errors to users.
    error_log("PDO connection failed: " . $e->getMessage());
    $pdo = null;
}

// Optional: create a mysqli connection for legacy code that needs it
try {
    $mysqli = mysqli_init();
    // Enable MYSQLI_REPORT_STRICT if you want exceptions on mysqli errors
    if (!$mysqli->real_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, (int)$DB_PORT)) {
        error_log('mysqli connect failed: ' . mysqli_connect_error());
        $db = null;
    } else {
        $db = $mysqli; // $db is a mysqli connection
    }
} catch (Throwable $e) {
    error_log('mysqli connect exception: ' . $e->getMessage());
    $db = null;
}

// Export $pdo and $db for including scripts
// Example usage in other files: require_once __DIR__ . '/api/db.php'; then use $pdo
?>