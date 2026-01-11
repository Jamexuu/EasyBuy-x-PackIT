<?php
// File: admin/replyFeedback.php

// 1. Adjust paths: Go up one level (..) to reach 'api/classes'
require_once '../api/classes/Auth.php';
require_once '../api/classes/Database.php';

// 2. Security: Ensure only Admins can access this
Auth::requireAdmin();

// 3. Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 4. Get and sanitize inputs
    $feedbackId = isset($_POST['feedback_id']) ? (int)$_POST['feedback_id'] : 0;
    $status     = isset($_POST['status']) ? trim($_POST['status']) : 'open';
    $adminReply = isset($_POST['admin_reply']) ? trim($_POST['admin_reply']) : '';

    // Validate ID
    if ($feedbackId > 0) {
        $db = new Database();
        $conn = $db->connect();

        // 5. Update the database
        // FIX: Removed 'updated_at = NOW()' to prevent the "Unknown column" error
        $sql = "UPDATE user_feedback 
                SET admin_reply = ?, 
                    status = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            // "ssi" stands for String (reply), String (status), Integer (id)
            $stmt->bind_param("ssi", $adminReply, $status, $feedbackId);
            
            if ($stmt->execute()) {
                // Success: Redirect back to dbTables with a success message
                header("Location: dbTables.php?view=userFeedback&msg=replied");
                exit;
            } else {
                // Database execution error
                header("Location: dbTables.php?view=userFeedback&error=db_error");
                exit;
            }
            $stmt->close();
        } else {
            // Statement preparation error
            header("Location: dbTables.php?view=userFeedback&error=stmt_error");
            exit;
        }
    } else {
        // Invalid ID provided
        header("Location: dbTables.php?view=userFeedback&error=invalid_id");
        exit;
    }

} else {
    // If accessed directly without POST, redirect back
    header("Location: dbTables.php?view=userFeedback");
    exit;
}
?>