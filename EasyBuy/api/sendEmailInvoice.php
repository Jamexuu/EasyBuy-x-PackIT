<?php

require_once 'classes/Mailer.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$mailer = new Mailer();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

try {
    $email = $data['email'] ?? '';
    $name = $data['name'] ?? '';
    $subject = $data['subject'] ?? 'Your EasyBuy Order Invoice';
    $items = $data['checkout_items'] ?? [];
    $subtotal = $data['subtotal'] ?? 0;
    $shipping_fee = $data['shipping_fee'] ?? 0;
    $total_weight = $data['total_weight'] ?? 0;
    $total_amount = $data['total_amount'] ?? 0;
    $attachment = $_FILES['attachment'] ?? null;

    if (empty($email)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email is required']);
        exit();
    }

    // Build invoice HTML
    $message = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>';
    $message .= '<div style="font-family:Arial,sans-serif;max-width:600px;margin:auto;">';
    $message .= '<h2 style="color:#6EC064;">EasyBuy Invoice</h2>';
    $message .= '<p>Hi ' . htmlspecialchars($name) . ',</p>';
    $message .= '<p>Thank you for your order! Here is your invoice:</p>';
    $message .= '<table style="width:100%;border-collapse:collapse;">';
    $message .= '<thead><tr style="background:#f2f2f2;"><th style="padding:8px;border:1px solid #ddd;text-align:left;">Item</th><th style="padding:8px;border:1px solid #ddd;text-align:center;">Qty</th><th style="padding:8px;border:1px solid #ddd;text-align:right;">Subtotal</th></tr></thead><tbody>';
    foreach ($items as $item) {
        $itemName = htmlspecialchars($item['product_name'] ?? '');
        $qty = intval($item['quantity'] ?? 1);
        $price = floatval($item['final_price'] ?? 0);
        $isSale = isset($item['is_sale']) && $item['is_sale'] == 1;
        $saleBadge = $isSale ? '<span style="color:#28a745;font-size:12px;">' . ($item['sale_percentage'] ?? '') . '% Off</span>' : '';
        $subtotalItem = number_format($price * $qty, 2);
        $message .= '<tr>';
        $message .= '<td style="padding:8px;border:1px solid #ddd;">' . $itemName . ' ' . $saleBadge . '</td>';
        $message .= '<td style="padding:8px;border:1px solid #ddd;text-align:center;">' . $qty . '</td>';
        $message .= '<td style="padding:8px;border:1px solid #ddd;text-align:right;">₱' . $subtotalItem . '</td>';
        $message .= '</tr>';
    }
    $message .= '</tbody></table>';
    $message .= '<div style="margin-top:16px;">';
    $message .= '<p><strong>Subtotal:</strong> ₱' . number_format($subtotal, 2) . '</p>';
    $message .= '<p><strong>Shipping Fee:</strong> ₱' . number_format($shipping_fee, 2) . '</p>';
    $message .= '<p><strong>Total Amount:</strong> ₱' . number_format($total_amount, 2) . '</p>';
    $message .= '</div>';
    $message .= '<p style="margin-top:24px;">If you have any questions, please contact us at support@easybuy.com.</p>';
    $message .= '<p style="color:#888;font-size:12px;">This is an automated email. Please do not reply.</p>';
    $message .= '</div></body></html>';


    $mailer = new Mailer();
    $mailer->setSender("thisis.acadpurposesonly@gmail.com", "EasyBuy Admin");
    $mailer->addRecipient($email, $name);

    if ($attachment && $attachment['error'] !== UPLOAD_ERR_NO_FILE) {
        $mailer->addAttachment($attachment);
    }
    
    $mailer->setSubject($subject);
    $mailer->isHTML(true); 
    $mailer->setBody(html_entity_decode($message));
    $mailer->send();

    echo json_encode(['success' => true, 'message' => 'Email sent successfully.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
