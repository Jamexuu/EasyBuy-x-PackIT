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

if (!$userRow) {
    // Avoid leaking whether email exists — show a success message anyway
    $_SESSION['fp_success'] = 'If an account exists for that email, a reset link was sent.';
    header('Location: forgotPassword.php');
    exit;
}

$token = $user->createPasswordResetToken($email, 60); // 60 minutes

if (!$token) {
    $_SESSION['fp_error'] = 'Could not create reset token. Try again later.';
    header('Location: forgotPassword.php');
    exit;
}

// Build reset URL. Adjust path if your site is served from a subfolder.
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // should be /PackIT/frontend
$resetLink = $protocol . '://' . $host . $basePath . '/resetPassword.php?token=' . urlencode($token);

// Email content
$subject = "PackIT password reset";
$body = "
<p>Hello,</p>
<p>We received a request to reset your PackIT account password. Click the link below to set a new password (link expires in 60 minutes):</p>
<p><a href=\"$resetLink\">Reset password</a></p>
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

$_SESSION['fp_success'] = 'If an account exists for that email, a reset link was sent.';
if (!$sent) {
    // Do not reveal too much; but log server-side if needed
    // error_log("Failed to send password reset email to $email");
    $_SESSION['fp_success'] = 'A reset link was created. (Email sending failed; contact admin.)';
}

header('Location: forgotPassword.php');
exit;