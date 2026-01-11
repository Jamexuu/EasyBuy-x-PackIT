<?php
$vendorPath = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($vendorPath)) {
    die("Vendor autoload not found at: " . realpath(__DIR__ . '/..') . "/vendor/autoload.php");
}

require_once $vendorPath;

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();
