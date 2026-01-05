<?php
declare(strict_types=1);

require_once __DIR__ . '/../classes/Database.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
  }

  // Require login
  $user = $_SESSION['user'] ?? null;
  $userId = isset($user['id']) ? (int)$user['id'] : 0;
  if ($userId <= 0) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
  }

  // CSRF
  $token = $_POST['csrf_token'] ?? '';
  if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string)$token)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
  }

  if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded or upload error']);
    exit;
  }

  // Validate file
  $file = $_FILES['avatar'];
  $maxSize = 2 * 1024 * 1024; // 2MB
  if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['error' => 'File too large (max 2MB)']);
    exit;
  }

  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($file['tmp_name']);
  $allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
  ];
  if (!isset($allowed[$mime])) {
    http_response_code(400);
    echo json_encode(['error' => 'Unsupported image type']);
    exit;
  }

  $ext = $allowed[$mime];
  $baseDir = realpath(__DIR__ . '/../../uploads/avatars');
  if ($baseDir === false) {
    $baseDir = __DIR__ . '/../../uploads/avatars';
  }
  if (!is_dir($baseDir)) {
    @mkdir($baseDir, 0775, true);
  }

  $filename = 'user_' . $userId . '.' . $ext;
  $destPath = $baseDir . DIRECTORY_SEPARATOR . $filename;

  if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save file']);
    exit;
  }

  // Build absolute URL (fixes broken path on localhost/EasyBuy-x-PackIT/PackIT)
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
  // Adjust basePrefix to your deployment root if needed
  $basePrefix = '/EasyBuy-x-PackIT/PackIT';
  $webPathAbs = $scheme . '://' . $host . $basePrefix . '/uploads/avatars/' . $filename;

  // Save to DB
  $db = new Database();
  $db->executeQuery("UPDATE users SET profile_image = ? WHERE id = ?", [$webPathAbs, (string)$userId]);

  // Update session
  $_SESSION['user']['profile_image'] = $webPathAbs;

  echo json_encode(['ok' => true, 'path' => $webPathAbs]);
  exit;
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
  exit;
}