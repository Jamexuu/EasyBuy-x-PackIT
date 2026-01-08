<?php
// api/db.php
// Lightweight adapter: create $pdo (PDO|null) for new code, and keep legacy mysqli Database available.
// Path: project-root/api/db.php

declare(strict_types=1);

$DB_HOST = 'localhost';
$DB_NAME = 'packit';
$DB_USER = 'root';
$DB_PASS = '';
$DB_CHARSET = 'utf8mb4';

// Create PDO for code that expects $pdo
$pdo = null;
try {
    $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log('PDO connection failed: ' . $e->getMessage());
    $pdo = null;
}

// Also require your existing Database class (mysqli) so legacy code continues to work.
$db = null;
$databaseClassPath = __DIR__ . '/classes/Database.php';
if (file_exists($databaseClassPath)) {
    try {
        require_once $databaseClassPath;
        if (class_exists('Database')) {
            $Database = new Database();
            try {
                $dbConn = $Database->connect();
                $db = $dbConn; // mysqli connection
            } catch (Throwable $e) {
                error_log('Legacy mysqli Database connect failed: ' . $e->getMessage());
                $db = null;
            }
        }
    } catch (Throwable $e) {
        error_log('Could not load Database class: ' . $e->getMessage());
        $db = null;
    }
}

// Now $pdo (PDO|null) and $db (mysqli|null) are available to including scripts.
?>