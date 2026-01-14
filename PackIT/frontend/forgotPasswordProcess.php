<?php
session_start();
require_once __DIR__ . '/../api/classes/User.php';

// require Gmail helper if you have it
$gmailSendAvailable = false;
if (file_exists(__DIR__ . '/../api/gmail/sendMail.php')) {
    require_once __DIR__ . '/../api/gmail/sendMail.php';
    $gmailSendAvailable = function_exists('sendMail');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgotPassword.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['fp_error'] = 'Please enter a valid email.';
    header('Location: forgotPassword.php');
    exit;
}

$user = new User();
$userRow = $user->findByEmail($email);

// Avoid revealing whether email exists: still create behavior but if no user, show generic success
if (!$userRow) {
    $_SESSION['fp_success'] = 'If an account exists for that email, an OTP was sent.';
    header('Location: forgotPassword.php');
    exit;
}

// Create numeric OTP (e.g. 6 digits) valid for 3 minutes
$otp = $user->createPasswordResetOTP($email, 3, 6);

if (!$otp) {
    $_SESSION['fp_error'] = 'Could not create reset OTP. Try again later.';
    header('Location: forgotPassword.php');
    exit;
}

// Email content with OTP
$subject = "PackIT password reset code";
$body = "
<p>Hello,</p>
<p>We received a request to reset your PackIT account password. Use the following one-time code to verify and set a new password (code expires in 3 minutes):</p>
<h2 style='letter-spacing:4px;'>$otp</h2>
<p>If you didn't request this, you can safely ignore this message.</p>
";

// Try to send using sendMail() if available, otherwise fallback to mail()
$sent = false;
try {
    if ($gmailSendAvailable) {
        // sendMail($to, $subject, $htmlBody)
        $sent = sendMail($email, $subject, $body);
    } else {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: PackIT <no-reply@packit.local>\r\n";
        $sent = mail($email, $subject, $body, $headers);
    }
} catch (Throwable $e) {
    $sent = false;
}

// Redirect to OTP entry page (keep message generic)
$_SESSION['fp_success'] = 'If an account exists for that email, an OTP was sent.';
if (!$sent) {
    // Do not reveal too much; but log server-side if needed
    // error_log("Failed to send OTP to $email");
    $_SESSION['fp_success'] = 'An OTP was created. (Email sending failed; contact admin.)';
}

// Redirect user to the OTP verify page. Provide email as query param for convenience (not required)
header('Location: verifyOTP.php?email=' . urlencode($email));
exit;