<?php
require_once 'classes/Auth.php';

Auth::logout();

header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Logged out']);
exit();