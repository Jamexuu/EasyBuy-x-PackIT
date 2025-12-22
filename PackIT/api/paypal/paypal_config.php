<?php
// paypal_config.php
declare(strict_types=1);

// CLIENT ID and SECRET from your Developer Dashboard [cite: 51, 52]
// NOTE: These are SANDBOX credentials. Change to Live for production[cite: 53].
define('PAYPAL_CLIENT_ID', 'AQbYWn0ymUSZaWIG69hehzgGwXus-Ntt0q21sVjdHuAIdVoUkexHRNFVsZZtmlHxe5pky_qoGUgJatmE');
define('PAYPAL_SECRET', 'ELAAQTqDXoVV1NxCSnf4jQxS8XtG8N4GHbumuBnks-iNRJbTQ2yp_ycioBwYbmiaCOKKz0_jxY6fpJfd');

// Sandbox URL for testing [cite: 62, 145]
define('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com');
?>