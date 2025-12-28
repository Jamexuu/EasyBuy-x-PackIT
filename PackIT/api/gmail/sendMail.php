<?php
// sendMail.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php'; // PHPMailer via Composer

/**
 * Send email from PackIT project email
 *
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $html HTML content of the email
 * @return bool
 * @throws Exception
 */
function sendMail(string $to, string $subject, string $html): bool {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'packit.notification@gmail.com'; // Project email
        $mail->Password   = 'xrfb bpis snrn dlec';        // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Sender & Recipient
        $mail->setFrom('packit.notification@gmail.com', 'PackIT Delivery');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html;

        $mail->send();
        return true;
    } catch (Exception $e) {
        throw new Exception("Email could not be sent: {$mail->ErrorInfo}");
    }
}
