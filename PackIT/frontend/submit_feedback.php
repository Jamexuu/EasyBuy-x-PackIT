<?php
// frontend/submit_feedback.php
// Accepts POST feedback from authenticated users and stores into user_feedback table.
// Returns JSON { success: bool, message: string, id?: int }

declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

session_start();

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// User must be logged in
$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

// CSRF protection
$csrf = $_POST['csrf_token'] ?? '';
if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string)$csrf)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

// Basic sanitization and validation
$subject = trim((string)($_POST['subject'] ?? ''));
$category = trim((string)($_POST['category'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($message === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter your feedback.']);
    exit;
}
if (mb_strlen($message) > 2000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Feedback too long (max 2000 characters).']);
    exit;
}
if ($subject === '') {
    $subject = mb_substr($message, 0, 120);
}

// Load DB adapter (expects $pdo to be provided by api/db.php)
$pdo = null;
$dbPath = __DIR__ . '/../api/db.php';
if (file_exists($dbPath)) {
    try {
        require_once $dbPath; // should set $pdo to a PDO instance
    } catch (Throwable $e) {
        error_log("Could not require api/db.php: " . $e->getMessage());
    }
}

if (!isset($pdo) || !($pdo instanceof PDO)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database not available.']);
    exit;
}

// Ensure the session user exists and is role = 'user' (feedback only for regular users)
try {
    $stmt = $pdo->prepare("SELECT id, role FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $userId]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$u) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid user.']);
        exit;
    }
    if (($u['role'] ?? '') !== 'user') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Only normal users are allowed to submit feedback.']);
        exit;
    }
} catch (Throwable $e) {
    error_log('DB user check failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error.']);
    exit;
}

// Insert feedback
try {
    $stmt = $pdo->prepare("INSERT INTO user_feedback (user_id, subject, category, message, user_agent, ip) VALUES (:user_id, :subject, :category, :message, :user_agent, :ip)");
    $stmt->execute([
        ':user_id'    => $userId,
        ':subject'    => $subject,
        ':category'   => $category ?: null,
        ':message'    => $message,
        ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ':ip'         => $_SERVER['REMOTE_ADDR'] ?? null
    ]);
    $insertId = (int)$pdo->lastInsertId();

    echo json_encode(['success' => true, 'message' => 'Thank you — your feedback has been submitted.', 'id' => $insertId]);
    exit;
} catch (Throwable $e) {
    error_log("Failed to save feedback: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not save feedback.']);
    exit;
}
?>