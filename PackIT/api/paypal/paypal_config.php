<?php
declare(strict_types=1);

$secretsFile = __DIR__ . '/paypal_secrets.php';

if (file_exists($secretsFile)) {
    require_once $secretsFile;
} else {
    if (!defined('PAYPAL_CLIENT_ID')) define('PAYPAL_CLIENT_ID', 'PLACEHOLDER_CLIENT_ID');
    if (!defined('PAYPAL_SECRET')) define('PAYPAL_SECRET', 'PLACEHOLDER_SECRET');
}

define('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com');
?>