<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!isset($_FILES['avatar'])) {
    echo json_encode(['ok' => false, 'error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['avatar'];
$allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

if (!in_array($file['type'], $allowed)) {
    echo json_encode(['ok' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF allowed.']);
    exit;
}

// Ensure uploads directory exists
$uploadDir = __DIR__ . '/../../uploads/avatars/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Generate unique name: avatar_USERID_TIMESTAMP.ext
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'avatar_' . $_SESSION['user']['id'] . '_' . time() . '.' . $ext;
$targetPath = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    // Update DB
    $db = new Database();
    
    // Relative path for frontend to access (adjust "api" vs "frontend" depth as needed)
    // Assuming api/ is parallel to frontend/, we need a path accessible via URL.
    // If your server root is the parent folder:
    $webPath = '../uploads/avatars/' . $filename; 

    $sql = "UPDATE users SET profile_image = ? WHERE id = ?";
    $stmt = $db->executeQuery($sql, [$webPath, (string)$_SESSION['user']['id']]);
    
    echo json_encode(['ok' => true, 'path' => $webPath]);
} else {
    echo json_encode(['ok' => false, 'error' => 'Failed to save file.']);
}
?>