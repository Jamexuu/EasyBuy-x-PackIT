<?php
session_start();
require_once __DIR__ . '/../api/classes/Database.php';
require_once __DIR__ . '/../frontend/components/autorefresh.php';
require_once __DIR__ . '/../api/sms/SmsNotificationService.php'; // SMS service

// Needed because you already use Dotenv in this file
require_once __DIR__ . '/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->safeLoad(); // loads PackIT/.env

// Email helper (PHPMailer)
require_once __DIR__ . '/../api/gmail/sendMail.php';

// Define $action to avoid undefined variable issues
$action = $_POST['action'] ?? $_GET['action'] ?? null;

if (!isset($_SESSION['driver_id'])) {
    header("Location: index.php");
    exit;
}

$db = new Database();
$driverId = (int)$_SESSION['driver_id'];

/* CSRF token */
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

/* Helper: determine next status button label */
function next_button_label($status)
{
    return match ($status) {
        'accepted' => 'Mark Picked Up',
        'picked_up' => 'Mark In Transit',
        'in_transit' => 'Mark Delivered',
        default => null,
    };
}

function next_easybuy_button_label($status)
{
    $statusLower = strtolower($status);
    return match ($statusLower) {
        'picked up' => 'Mark In Transit',
        'in transit' => 'Mark Delivered',
        default => null,
    };
}

function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
function money($n): string
{
    return number_format((float)$n, 2);
}
function status_label(string $status): string
{
    return strtoupper(str_replace('_', ' ', $status));
}
function fmtDims($l, $w, $h): string
{
    $fmt = fn($x) => rtrim(rtrim(number_format((float)$x, 1), '0'), '.');
    return $fmt($l) . " x " . $fmt($w) . " x " . $fmt($h) . " m";
}

/**
 * Create a simple HTML receipt for delivered bookings.
 * $audience: 'user' | 'driver'
 */
function buildDeliveredEmailHtml(array $row, string $audience): string
{
    $bookingId = (int)($row['id'] ?? 0);

    $userFirst = trim((string)($row['user_first_name'] ?? 'Customer'));
    if ($userFirst === '') $userFirst = 'Customer';

    $driverFirst = trim((string)($row['driver_first_name'] ?? 'Driver'));
    if ($driverFirst === '') $driverFirst = 'Driver';

    $pickup = trim((string)($row['pickup_municipality'] ?? '') . ', ' . (string)($row['pickup_province'] ?? ''));
    $drop   = trim((string)($row['drop_municipality'] ?? '') . ', ' . (string)($row['drop_province'] ?? ''));

    $vehicle = (string)($row['vehicle_type'] ?? '');

    $base = number_format((float)($row['base_amount'] ?? 0), 2);
    $distance = number_format((float)($row['distance_amount'] ?? 0), 2);
    $total = number_format((float)($row['total_amount'] ?? 0), 2);

    $createdAt = (string)($row['created_at'] ?? '');

    $greetingName = ($audience === 'driver') ? $driverFirst : $userFirst;
    $title = ($audience === 'driver') ? 'PackIT - Delivery Completed' : 'PackIT - Booking Delivered';
    $subtitle = ($audience === 'driver')
        ? 'You have completed a delivery. Here are the trip details:'
        : 'Your booking has been marked as Delivered. Thank you for using PackIT. Here are the details:';

    $html  = "<div style=\"font-family:Arial,Helvetica,sans-serif;color:#222;line-height:1.5\">";
    $html .= "<h2 style=\"color:#198754;margin:0 0 10px 0;\">" . h($title) . "</h2>";
    $html .= "<p style=\"margin:0 0 12px 0;\">Hi " . h($greetingName) . ",</p>";
    $html .= "<p style=\"margin:0 0 16px 0;\">" . h($subtitle) . "</p>";

    $html .= "<table style=\"width:100%;max-width:640px;border-collapse:collapse;border:1px solid #eee\">";
    $rowHtml = function (string $k, string $v) {
        return "<tr><td style=\"padding:10px;border-bottom:1px solid #eee;color:#6c757d;width:40%\">" . h($k) . "</td>"
            . "<td style=\"padding:10px;border-bottom:1px solid #eee;font-weight:600\">" . $v . "</td></tr>";
    };

    $html .= $rowHtml("Booking #", (string)$bookingId);
    if ($createdAt !== '') $html .= $rowHtml("Created", h($createdAt));
    if ($vehicle !== '') $html .= $rowHtml("Vehicle", h($vehicle));
    if ($pickup !== ',') $html .= $rowHtml("Pickup", h($pickup));
    if ($drop !== ',') $html .= $rowHtml("Drop-off", h($drop));

    // Money
    $html .= $rowHtml("Base fare", "₱" . h($base));
    $html .= $rowHtml("Distance fare", "₱" . h($distance));
    $html .= $rowHtml("Total", "<span style=\"color:#b8860b\">₱" . h($total) . "</span>");

    // Last row remove border-bottom
    $html .= "</table>";

    if ($audience !== 'driver') {
        $trackingUrl = "/EasyBuy-x-PackIT/PackIT/frontend/tracking.php?booking_id=" . $bookingId;
        $html .= "<p style=\"margin:16px 0 0 0;\">Tracking link: <a href=\"" . h($trackingUrl) . "\">" . h($trackingUrl) . "</a></p>";
    }

    $html .= "<p style=\"margin:14px 0 0 0;color:#6c757d;font-size:13px\">If you believe this is an error, please contact support.</p>";
    $html .= "</div>";

    return $html;
}

/* Handle POST actions */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = 'Invalid request';
        header('Location: driverBookings.php');
        exit;
    }

    $action = $_POST['action'] ?? '';
    $bookingId = (int)($_POST['booking_id'] ?? 0);

    /**
     * QUICK SMS (preset prompts)
     * - pickup prompts => send to SENDER (pickup_contact_number)
     * - drop-off prompts => send to RECIPIENT (drop_contact_number)
     */
    if ($action === 'quick_sms' && $bookingId > 0) {
        $prompt = $_POST['prompt'] ?? '';

        // Define your preset prompts here
        $allowedPrompts = [
            // Pickup = sender
            'pickup_arrived' => [
                'target' => 'sender',
                'text'   => "Hi! I'm at the pickup location. Where are you?",
            ],

            // Drop-off = recipient
            'drop_arrived' => [
                'target' => 'recipient',
                'text'   => "Hi! I'm at the drop-off location. Where are you?",
            ],

            // Can be useful for both; here we keep it for recipient (drop-off side).
            'on_the_way_to_drop' => [
                'target' => 'recipient',
                'text'   => "Hi! I'm on the way to the drop-off now. Please be ready.",
            ],

            // Another common one for sender (pickup side)
            'cant_find_pickup' => [
                'target' => 'sender',
                'text'   => "Hi! I'm at the pickup area but I can't find the exact location. Please call/text me.",
            ],
        ];

        if (!isset($allowedPrompts[$prompt])) {
            $_SESSION['flash_error'] = 'Invalid message prompt.';
            header('Location: driverBookings.php');
            exit;
        }

        // Ensure booking belongs to this driver, and fetch numbers
        $stmtB = $db->executeQuery(
            "SELECT b.pickup_contact_number, b.drop_contact_number
             FROM bookings b
             WHERE b.id = ? AND b.driver_id = ?
             LIMIT 1",
            [$bookingId, $driverId]
        );
        $bRow = $db->fetch($stmtB)[0] ?? null;

        if (!$bRow) {
            $_SESSION['flash_error'] = 'Booking not found or not assigned to you.';
            header('Location: driverBookings.php');
            exit;
        }

        $target = $allowedPrompts[$prompt]['target']; // sender|recipient
        $text   = $allowedPrompts[$prompt]['text'];

        $recipients = [];
        if ($target === 'sender' && !empty($bRow['pickup_contact_number'])) {
            $recipients[] = $bRow['pickup_contact_number'];
        }
        if ($target === 'recipient' && !empty($bRow['drop_contact_number'])) {
            $recipients[] = $bRow['drop_contact_number'];
        }

        if (empty($recipients)) {
            $_SESSION['flash_error'] = 'No valid contact number found for this message.';
            header('Location: driverBookings.php');
            exit;
        }

        $smsService = new SmsNotificationService();

        // Status stored in SmsLogs.Status (if you integrated logging)
        $smsService->notify(
            $recipients,
            $text,
            [
                'booking_id' => $bookingId,
                'driver_id'  => $driverId,
                'status'     => 'quick_sms_' . $prompt,
            ]
        );

        $_SESSION['flash_success'] = 'Quick message sent.';
        header('Location: driverBookings.php');
        exit;
    }

    /* Advance status */
    if ($action === 'advance' && $bookingId > 0) {
        $stmt = $db->executeQuery(
            "SELECT tracking_status FROM bookings WHERE id = ? AND driver_id = ? LIMIT 1",
            [$bookingId, $driverId]
        );
        $row = $db->fetch($stmt);

        if (empty($row)) {
            $_SESSION['flash_error'] = 'Booking not found or not assigned to you.';
        } else {
            $current = (string)$row[0]['tracking_status'];
            $nextMap = [
                'accepted'   => 'picked_up',
                'picked_up'  => 'in_transit',
                'in_transit' => 'delivered',
            ];

            if (!isset($nextMap[$current])) {
                $_SESSION['flash_error'] = 'Cannot advance status from "' . h($current) . '".';
            } else {
                $next = $nextMap[$current];

                $stmt2 = $db->executeQuery(
                    "UPDATE bookings
                     SET tracking_status = ?, updated_at = CURRENT_TIMESTAMP()
                     WHERE id = ? AND driver_id = ?",
                    [$next, $bookingId, $driverId]
                );

                $updated = (mysqli_stmt_affected_rows($stmt2) > 0);

                if ($updated) {
                    $_SESSION['flash_success'] = 'Booking status updated to ' . $next . '.';

                    // SMS notifications per status (sender + recipient)
                    $stmtBooking = $db->executeQuery(
                        "SELECT b.pickup_contact_number, b.drop_contact_number, d.first_name AS driver_name
                         FROM bookings b
                         JOIN drivers d ON b.driver_id = d.id
                         WHERE b.id = ? AND b.driver_id = ? LIMIT 1",
                        [$bookingId, $driverId]
                    );
                    $bookingDetails = $db->fetch($stmtBooking)[0] ?? null;

                    if ($bookingDetails) {
                        $smsService = new SmsNotificationService();

                        $recipients = [];
                        if (!empty($bookingDetails['pickup_contact_number'])) $recipients[] = $bookingDetails['pickup_contact_number'];
                        if (!empty($bookingDetails['drop_contact_number'])) $recipients[] = $bookingDetails['drop_contact_number'];

                        if (!empty($recipients)) {
                            $smsService->notify(
                                $recipients,
                                $smsService->getTemplate($next, [
                                    'booking_id'  => $bookingId,
                                    'driver_name' => $bookingDetails['driver_name'] ?? '',
                                ]),
                                [
                                    'booking_id' => $bookingId,
                                    'driver_id'  => $driverId,
                                    'status'     => $next,
                                ]
                            );
                        }
                    }

                    // When delivered: send receipt emails (user + driver), then go back to dashboard
                    if ($next === 'delivered') {
                        try {
                            $stmtReceipt = $db->executeQuery(
                                "SELECT b.id, b.user_id, b.driver_id,
                                        b.vehicle_type, b.base_amount, b.distance_amount, b.total_amount,
                                        b.pickup_municipality, b.pickup_province,
                                        b.drop_municipality, b.drop_province,
                                        b.created_at,
                                        u.email AS user_email, u.first_name AS user_first_name,
                                        d.email AS driver_email, d.first_name AS driver_first_name
                                 FROM bookings b
                                 JOIN users u ON b.user_id = u.id
                                 JOIN drivers d ON b.driver_id = d.id
                                 WHERE b.id = ? AND b.driver_id = ?
                                 LIMIT 1",
                                [$bookingId, $driverId]
                            );
                            $receiptRows = $db->fetch($stmtReceipt);
                            $receipt = $receiptRows[0] ?? null;

                            if ($receipt) {
                                $bookingNum = (int)$receipt['id'];

                                $userEmail = trim((string)($receipt['user_email'] ?? ''));
                                $driverEmail = trim((string)($receipt['driver_email'] ?? ''));

                                if ($userEmail !== '') {
                                    $subjectUser = "PackIT Receipt — Booking #{$bookingNum} (Delivered)";
                                    $htmlUser = buildDeliveredEmailHtml($receipt, 'user');
                                    sendMail($userEmail, $subjectUser, $htmlUser);
                                }

                                if ($driverEmail !== '') {
                                    $subjectDriver = "PackIT Delivery Completed — Booking #{$bookingNum}";
                                    $htmlDriver = buildDeliveredEmailHtml($receipt, 'driver');
                                    sendMail($driverEmail, $subjectDriver, $htmlDriver);
                                }
                            }
                        } catch (Exception $e) {
                            // Do not break flow if mail fails
                            $logMsg = sprintf("[%s] sendMail error (delivered) booking %d: %s\n", date('c'), $bookingId, $e->getMessage());
                            @file_put_contents(__DIR__ . '/../api/gmail/sendmail_log.txt', $logMsg, FILE_APPEND);
                            error_log($logMsg);
                        }

                        header('Location: driver.php');
                        exit;
                    }
                } else {
                    $_SESSION['flash_error'] = 'Failed to update booking status. Try again.';
                }
            }
        }
    }

    /* Advance EasyBuy order status */
    if ($action === 'advance_easybuy') {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $currentStatus = $_POST['current_status'] ?? '';

        if ($orderId > 0) {
            $nextMap = [
                'picked up'     => 'in transit',
                'in transit'    => 'order arrived',
            ];

            $next = $nextMap[strtolower($currentStatus)] ?? null;

            if ($next) {
                // Call driver-specific EasyBuy API to update status
                $easybuyIP = $_ENV['EASYBUY_IP'] ?? 'localhost';
                $updateUrl = "http://$easybuyIP/EasyBuy-x-PackIT/EasyBuy/api/updateOrderStatusByDriver.php";

                $postData = json_encode([
                    'orderId' => $orderId,
                    'driverId' => $driverId,
                    'newStatus' => $next
                ]);

                $context = stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => 'Content-Type: application/json',
                        'content' => $postData,
                        'timeout' => 5,
                        'ignore_errors' => true
                    ]
                ]);

                $response = @file_get_contents($updateUrl, false, $context);
                $result = json_decode($response, true);

                if ($response !== false && isset($result['success']) && $result['success']) {
                    $_SESSION['flash_success'] = 'EasyBuy order status updated to ' . $next . '.';

                    if ($next === 'order arrived') {
                        header('Location: driver.php');
                        exit;
                    }
                } else {
                    $errorMsg = $result['error'] ?? 'Failed to update EasyBuy order status.';
                    $_SESSION['flash_error'] = $errorMsg;
                }
            } else {
                $_SESSION['flash_error'] = 'Cannot advance status from "' . h($currentStatus) . '".';
            }
        }
    }

    header('Location: driverBookings.php');
    exit;
}

/* Fetch driver name */
$stmt = $db->executeQuery("SELECT first_name FROM drivers WHERE id = ? LIMIT 1", [$driverId]);
$rows = $db->fetch($stmt);
$driverName = !empty($rows) ? $rows[0]['first_name'] : 'Driver';

/* Fetch active bookings assigned to this driver (includes user contact_number) */
$stmtMine = $db->executeQuery(
    "SELECT b.*,
            u.first_name AS user_first_name,
            u.last_name AS user_last_name,
            u.contact_number AS user_contact_number,
            u.email AS user_email
     FROM bookings b
     JOIN users u ON b.user_id = u.id
     WHERE b.driver_id = ? AND b.tracking_status IN ('accepted','picked_up','in_transit')
     ORDER BY b.created_at ASC",
    [$driverId]
);
$myBookings = $db->fetch($stmtMine);

$easybuyOrders = [];
try {
    $easybuyIP = $_ENV['EASYBUY_IP'] ?? 'localhost';
    $easybuyApiUrl = "http://$easybuyIP/EasyBuy-x-PackIT/EasyBuy/api/getAllOrders.php";

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 5,
            'ignore_errors' => true
        ]
    ]);

    $response = @file_get_contents($easybuyApiUrl, false, $context);

    if ($response !== false) {
        $allEasybuyOrders = json_decode($response, true);

        if (is_array($allEasybuyOrders)) {
            // Filter only active orders (exclude completed "order arrived")
            foreach ($allEasybuyOrders as $order) {
                $status = strtolower($order['status'] ?? '');
                if (in_array($status, ['picked up', 'in transit'])) {
                    $easybuyOrders[] = $order;
                }
            }
        }
    }
} catch (Exception $e) {
    // Silently fail if EasyBuy API is not available
    error_log('Failed to fetch EasyBuy orders: ' . $e->getMessage());
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Driver Bookings | PackIT</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --secondary-teal: #203a43;
            --card-border: #203a43;
        }

        body {
            background-color: #f4f6f8;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-content {
            flex: 1;
            padding: 3rem 1rem;
        }

        .main-container {
            background: #ffffff;
            border-radius: 15px;
            border: 2px solid var(--card-border);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            min-height: 500px;
        }

        .status-badge-green,
        .status-badge-gray {
            font-weight: 700;
            font-size: 0.8rem;
            padding: 6px 16px;
            border-radius: 20px;
            display: inline-block;
        }

        .status-badge-green {
            background-color: #c9f29d;
            color: #2c5206;
        }

        .status-badge-gray {
            background-color: #e9ecef;
            color: #495057;
        }

        .detail-card {
            background-color: #f8f9fa;
            border-radius: 15px;
            border: 1px solid #e9ecef;
            padding: 1.25rem;
        }

        .kv {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.08);
        }

        .kv:last-child {
            border-bottom: none;
        }

        .kv .k {
            color: #6c757d;
            font-size: .85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .kv .v {
            color: #212529;
            font-weight: 700;
            text-align: right;
        }

        .empty-state-container {
            text-align: center;
            padding: 4rem 1rem;
            color: #6c757d;
        }

        .empty-state-img {
            width: 150px;
            margin-bottom: 1.5rem;
            opacity: 0.8;
        }

        .list-scroll {
            max-height: 70vh;
            overflow: auto;
            padding-right: .25rem;
        }
    </style>
</head>

<body>

    <?php include __DIR__ . "/../frontend/components/driverNavbar.php"; ?>

    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="main-container p-4 p-md-5">

                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                            <div>
                                <h4 class="fw-bold mb-1" style="color: var(--secondary-teal);">
                                    <span class="material-symbols-outlined align-middle me-2">inventory_2</span>
                                    Active Booking Details
                                </h4>
                                <div class="text-muted small">
                                    Hi <?= h($driverName) ?> — update status and view complete booking details.
                                </div>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <a href="driver.php" class="btn btn-outline-dark fw-bold px-3 shadow-sm text-uppercase">
                                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                                </a>
                            </div>
                        </div>

                        <?php if (!empty($_SESSION['flash_success'])): ?>
                            <div class="alert alert-success"><?= h($_SESSION['flash_success']); ?></div>
                            <?php unset($_SESSION['flash_success']); ?>
                        <?php endif; ?>

                        <?php if (!empty($_SESSION['flash_error'])): ?>
                            <div class="alert alert-danger"><?= h($_SESSION['flash_error']); ?></div>
                            <?php unset($_SESSION['flash_error']); ?>
                        <?php endif; ?>

                        <?php if (empty($myBookings) && empty($easybuyOrders)): ?>
                            <div class="empty-state-container">
                                <img src="../assets/box.png" alt="No bookings" class="empty-state-img">
                                <h4 class="fw-bold text-dark">No Active Booking</h4>
                                <p class="mb-0">Accept a booking from the dashboard to start delivering.</p>
                            </div>
                        <?php else: ?>
                            <div class="list-scroll">
                                <!-- PackIT Bookings -->
                                <?php foreach ($myBookings as $b):
                                    $status = (string)($b['tracking_status'] ?? '');
                                    $btnLabel = next_button_label($status);

                                    $pickupFull = implode(', ', array_filter([
                                        $b['pickup_house'] ?? null,
                                        $b['pickup_barangay'] ?? null,
                                        $b['pickup_municipality'] ?? null,
                                        $b['pickup_province'] ?? null,
                                    ]));

                                    $dropFull = implode(', ', array_filter([
                                        $b['drop_house'] ?? null,
                                        $b['drop_barangay'] ?? null,
                                        $b['drop_municipality'] ?? null,
                                        $b['drop_province'] ?? null,
                                    ]));

                                    $customerName = trim(($b['user_first_name'] ?? '') . ' ' . ($b['user_last_name'] ?? ''));
                                    $customerCp = (string)($b['user_contact_number'] ?? '');

                                    $pkgQty = (int)($b['package_quantity'] ?? 1);
                                    $pkgDesc = (string)($b['package_desc'] ?? '');
                                    $pkgType = (string)($b['package_type'] ?? '');
                                    $dims = fmtDims($b['size_length_m'] ?? 0, $b['size_width_m'] ?? 0, $b['size_height_m'] ?? 0);
                                ?>
                                    <div class="detail-card mb-4">
                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                                            <div>
                                                <h5 class="fw-bold mb-1" style="color: var(--secondary-teal);">
                                                    Booking #<?= (int)$b['id'] ?>
                                                </h5>
                                                <div class="text-muted small">
                                                    Customer: <strong><?= h($customerName ?: '—') ?></strong>
                                                    <?php if ($customerCp !== ''): ?>
                                                        • CP: <strong><?= h($customerCp) ?></strong>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <span class="status-badge-gray">VEHICLE: <?= h($b['vehicle_type'] ?? '—') ?></span>
                                                <span class="status-badge-green"><?= h(status_label($status)) ?></span>
                                            </div>
                                        </div>

                                        <hr class="my-3 text-muted opacity-25">

                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="detail-card">
                                                    <div class="fw-bold mb-2"><i class="bi bi-box-seam me-1"></i> Package</div>
                                                    <div class="kv">
                                                        <div class="k">Quantity</div>
                                                        <div class="v"><?= (int)$pkgQty ?></div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">Description</div>
                                                        <div class="v"><?= h($pkgDesc !== '' ? $pkgDesc : '—') ?></div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">Type</div>
                                                        <div class="v"><?= h($pkgType !== '' ? $pkgType : '—') ?></div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">Max KG</div>
                                                        <div class="v"><?= (int)($b['max_kg'] ?? 0) ?> kg</div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">Size</div>
                                                        <div class="v"><?= h($dims) ?></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="detail-card">
                                                    <div class="fw-bold mb-2"><i class="bi bi-geo-alt me-1"></i> Pickup (Sender)</div>
                                                    <div class="text-muted small mb-2"><?= h($pickupFull ?: '—') ?></div>
                                                    <div class="kv">
                                                        <div class="k">Pickup Contact</div>
                                                        <div class="v"><?= h(($b['pickup_contact_name'] ?? '—') . ' • ' . ($b['pickup_contact_number'] ?? '—')) ?></div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">Province</div>
                                                        <div class="v"><?= h($b['pickup_province'] ?? '—') ?></div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">Municipality</div>
                                                        <div class="v"><?= h($b['pickup_municipality'] ?? '—') ?></div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">Barangay</div>
                                                        <div class="v"><?= h($b['pickup_barangay'] ?? '—') ?></div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">House</div>
                                                        <div class="v"><?= h($b['pickup_house'] ?? '—') ?></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="detail-card">
                                                    <div class="fw-bold mb-2"><i class="bi bi-flag me-1"></i> Drop-off (Recipient)</div>
                                                    <div class="text-muted small mb-2"><?= h($dropFull ?: '—') ?></div>
                                                    <div class="kv">
                                                        <div class="k">Recipient</div>
                                                        <div class="v"><?= h(($b['drop_contact_name'] ?? '—') . ' • ' . ($b['drop_contact_number'] ?? '—')) ?></div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">Province</div>
                                                        <div class="v"><?= h($b['drop_province'] ?? '—') ?></div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">Municipality</div>
                                                        <div class="v"><?= h($b['drop_municipality'] ?? '—') ?></div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">Barangay</div>
                                                        <div class="v"><?= h($b['drop_barangay'] ?? '—') ?></div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">House</div>
                                                        <div class="v"><?= h($b['drop_house'] ?? '—') ?></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="detail-card">
                                                    <div class="fw-bold mb-2"><i class="bi bi-receipt me-1"></i> Fare & Payment</div>

                                                    <div class="kv">
                                                        <div class="k">Base Amount</div>
                                                        <div class="v">₱ <?= h(money($b['base_amount'] ?? 0)) ?></div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">Distance Amount</div>
                                                        <div class="v">₱ <?= h(money($b['distance_amount'] ?? 0)) ?></div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">Total Amount</div>
                                                        <div class="v text-warning">₱ <?= h(money($b['total_amount'] ?? 0)) ?></div>
                                                    </div>

                                                    <div class="kv">
                                                        <div class="k">Payment Status</div>
                                                        <div class="v"><?= h($b['payment_status'] ?? '—') ?></div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">Payment Method</div>
                                                        <div class="v"><?= h($b['payment_method'] ?? '—') ?></div>
                                                    </div>

                                                    <div class="kv">
                                                        <div class="k">Created</div>
                                                        <div class="v"><?= h($b['created_at'] ?? '—') ?></div>
                                                    </div>
                                                    <div class="kv">
                                                        <div class="k">Updated</div>
                                                        <div class="v"><?= h($b['updated_at'] ?? '—') ?></div>
                                                    </div>

                                                    <!-- QUICK MESSAGES UI -->
                                                    <div class="detail-card mt-3">
                                                        <div class="fw-bold mb-2"><i class="bi bi-chat-dots me-1"></i> Quick Messages</div>
                                                        <div class="text-muted small mb-3">
                                                            Pickup messages go to the <strong>sender</strong>. Drop-off messages go to the <strong>recipient</strong>.
                                                        </div>

                                                        <div class="d-flex flex-wrap gap-2">
                                                            <!-- Sender (pickup) -->
                                                            <form method="post" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                                                                <input type="hidden" name="action" value="quick_sms">
                                                                <input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
                                                                <input type="hidden" name="prompt" value="pickup_arrived">
                                                                <button type="submit" class="btn btn-outline-primary btn-sm fw-bold">
                                                                    Sender: I'm at pickup
                                                                </button>
                                                            </form>

                                                            <form method="post" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                                                                <input type="hidden" name="action" value="quick_sms">
                                                                <input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
                                                                <input type="hidden" name="prompt" value="cant_find_pickup">
                                                                <button type="submit" class="btn btn-outline-secondary btn-sm fw-bold">
                                                                    Sender: Can't find pickup
                                                                </button>
                                                            </form>

                                                            <!-- Recipient (drop-off) -->
                                                            <form method="post" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                                                                <input type="hidden" name="action" value="quick_sms">
                                                                <input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
                                                                <input type="hidden" name="prompt" value="on_the_way_to_drop">
                                                                <button type="submit" class="btn btn-outline-dark btn-sm fw-bold">
                                                                    Recipient: On the way
                                                                </button>
                                                            </form>

                                                            <form method="post" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                                                                <input type="hidden" name="action" value="quick_sms">
                                                                <input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
                                                                <input type="hidden" name="prompt" value="drop_arrived">
                                                                <button type="submit" class="btn btn-outline-dark btn-sm fw-bold">
                                                                    Recipient: I'm at drop-off
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <!-- END QUICK MESSAGES UI -->

                                                    <div class="mt-3 text-md-end">
                                                        <?php if ($btnLabel !== null): ?>
                                                            <form method="post" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                                                                <input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
                                                                <input type="hidden" name="action" value="advance">
                                                                <button type="submit" class="btn btn-warning fw-bold px-4 py-2 shadow-sm text-uppercase w-100 w-md-auto">
                                                                    <?= h($btnLabel) ?>
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <span class="status-badge-gray">NO ACTIONS</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <!-- EasyBuy Orders -->
                                <?php foreach ($easybuyOrders as $order):
                                    $orderId = $order['orderID'] ?? 0;
                                    $customerName = ($order['firstName'] ?? '') . ' ' . ($order['lastName'] ?? '');
                                    $status = $order['status'] ?? 'pending';
                                    $btnLabel = next_easybuy_button_label($status);

                                    $addr = $order['address'] ?? [];
                                    $deliveryAddress = implode(', ', array_filter([
                                        $addr['houseNumber'] ?? null,
                                        $addr['street'] ?? null,
                                        $addr['barangay'] ?? null,
                                        $addr['city'] ?? null,
                                        $addr['province'] ?? null,
                                    ]));
                                ?>
                                    <div class="detail-card mb-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="fw-bold mb-1" style="color: var(--secondary-teal);">
                                                    <i class="bi bi-bag-check me-2"></i>
                                                    EasyBuy Order #<?= h($orderId) ?>
                                                </h5>
                                                <span class="status-badge-gray"><?= h(strtoupper($status)) ?></span>
                                                <span class="status-badge-gray">EASYBUY</span>
                                            </div>
                                            <div class="fw-bold text-warning" style="font-size: 1.1rem;">
                                                ₱<?= h(number_format((float)($order['totalAmount'] ?? 0), 2)) ?>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="fw-bold small mb-2">Customer</div>
                                                <div class="kv">
                                                    <div class="k">Name</div>
                                                    <div class="v"><?= h($customerName) ?></div>
                                                </div>
                                                <div class="kv">
                                                    <div class="k">Contact</div>
                                                    <div class="v"><?= h($order['contactNumber'] ?? 'N/A') ?></div>
                                                </div>
                                                <div class="kv">
                                                    <div class="k">Email</div>
                                                    <div class="v"><?= h($order['userEmail'] ?? 'N/A') ?></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="fw-bold small mb-2">Delivery Address</div>
                                                <div class="kv">
                                                    <div class="k">Address</div>
                                                    <div class="v"><?= h($deliveryAddress ?: 'N/A') ?></div>
                                                </div>
                                                <div class="fw-bold small mt-3 mb-2">Order Info</div>
                                                <div class="kv">
                                                    <div class="k">Items</div>
                                                    <div class="v"><?= (int)($order['itemCount'] ?? 0) ?> items</div>
                                                </div>
                                                <div class="kv">
                                                    <div class="k">Payment</div>
                                                    <div class="v"><?= h($order['paymentMethod'] ?? 'N/A') ?></div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="my-3">

                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-info text-dark">EasyBuy delivery in progress</span>

                                            <?php if ($btnLabel !== null): ?>
                                                <form method="post" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                                                    <input type="hidden" name="order_id" value="<?= (int)$orderId ?>">
                                                    <input type="hidden" name="current_status" value="<?= h($status) ?>">
                                                    <input type="hidden" name="action" value="advance_easybuy">
                                                    <button type="submit" class="btn btn-warning fw-bold px-4 py-2 shadow-sm text-uppercase">
                                                        <?= h($btnLabel) ?>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="status-badge-gray">NO ACTIONS</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . "/../frontend/components/driverFooter.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>