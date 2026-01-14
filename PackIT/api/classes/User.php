<?php

include 'Database.php';

class User {
    private $db;

    function __construct(){
        $this->db = new Database();
    }

    function register($userData, $addressData, $role = 'user'){
        $userId = $this->insertUser($userData, $role);
        $addressData['userId'] = $userId;
        $this->insertAddress($addressData);
        return $userId;
    }

    function insertUser($userData, $role = 'user'){
        $sql = "INSERT INTO users (first_name, last_name, email, password, contact_number, role) 
            VALUES (?, ?, ?, ?, ?, ?)";
        $hashPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        $params = [
            $userData['firstName'],
            $userData['lastName'],
            $userData['email'],
            $hashPassword,
            $userData['contactNumber'],
            $role
        ];
        $stmt = $this->db->executeQuery($sql, $params);
        mysqli_stmt_close($stmt);
        return $this->db->lastInsertId();
    }

    function insertAddress($addressData){
        $sql = "INSERT INTO addresses (user_id, house_number, street, subdivision, landmark, barangay, city, province, postal_code) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $addressData['userId'],
            $addressData['houseNumber'] ?? '',
            $addressData['street'] ?? '',
            $addressData['subdivision'] ?? '',
            $addressData['landmark'] ?? '',
            $addressData['barangay'] ?? '',
            $addressData['city'],
            $addressData['province'],
            $addressData['postalCode']
        ];
        $stmt = $this->db->executeQuery($sql, $params);
        mysqli_stmt_close($stmt);
    }

    function login($email, $password, $requiredRole = null){
        $sql = "SELECT * FROM users WHERE email = ?";
        $params = [$email];
        $stmt = $this->db->executeQuery($sql, $params);
        $result = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        $data = $result[0] ?? null;

        if (!$data || !password_verify($password, $data['password'])) {
            return false;
        }

        if ($requiredRole !== null && $data) {
            if (! isset($data['role']) || $data['role'] !== $requiredRole) {
                return false;
            }
        }
        return $data;
    }

    function getUserDetails($userId){
        // Added u.profile_image to the selection
        $sql = "SELECT u.id, u.first_name, u.last_name, u.email, u.contact_number, u.role, u.created_at, u.profile_image,
                       a.house_number, a. street, a.subdivision, a. landmark, a.barangay, 
                       a.city, a.province, a.postal_code
                FROM users u
                LEFT JOIN addresses a ON u.id = a.user_id
                WHERE u.id = ?
                ORDER BY a.id DESC LIMIT 1"; 
        
        $params = [$userId];
        $stmt = $this->db->executeQuery($sql, $params);
        $result = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        
        return $result[0] ?? null;
    }

    // --- New Method: Update Profile ---
    function updateProfile(int $userId, array $userData, array $addressData) {
        // 1. Update Basic Info
        $sqlUser = "UPDATE users SET first_name = ?, last_name = ?, contact_number = ? WHERE id = ?";
        $paramsUser = [
            $userData['firstName'], 
            $userData['lastName'], 
            $userData['contactNumber'], 
            (string)$userId
        ];
        $stmt = $this->db->executeQuery($sqlUser, $paramsUser);
        mysqli_stmt_close($stmt);

        // 2. Update or Insert Address
        // Check if user has an address entry
        $check = $this->db->executeQuery("SELECT id FROM addresses WHERE user_id = ? ORDER BY id DESC LIMIT 1", [(string)$userId]);
        $rows = $this->db->fetch($check);
        mysqli_stmt_close($check);

        if (!empty($rows)) {
            // Update existing
            $addrId = $rows[0]['id'];
            $sqlAddr = "UPDATE addresses SET house_number=?, street=?, subdivision=?, landmark=?, barangay=?, city=?, province=?, postal_code=? WHERE id=?";
            $paramsAddr = [
                $addressData['houseNumber'],
                $addressData['street'],
                $addressData['subdivision'],
                $addressData['landmark'],
                $addressData['barangay'],
                $addressData['city'],
                $addressData['province'],
                $addressData['postalCode'],
                (string)$addrId
            ];
            $stmtAddr = $this->db->executeQuery($sqlAddr, $paramsAddr);
            mysqli_stmt_close($stmtAddr);
        } else {
            // Insert new
            $addressData['userId'] = $userId;
            $this->insertAddress($addressData);
        }

        return true;
    }

    function emailExists($email){
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->db->executeQuery($sql, [$email]);
        $rows = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        return !empty($rows);
    }

    // ... [Keep your existing password methods (changePassword, reset, otp) here] ...
    function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->db->executeQuery($sql, [$email]);
        $rows = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        return $rows[0] ?? null;
    }

        // --- Existing password reset methods ---

    /**
     * Return user row or null
     */
    /**
     * Create a password reset token for the user (if email exists).
     * Returns token string on success, false on failure.
     */

    function changePassword(int $userId, string $currentPassword, string $newPassword) {
        // Fetch user current hash
        $sql = "SELECT password FROM users WHERE id = ? LIMIT 1";
        $stmt = $this->db->executeQuery($sql, [(string)$userId]);
        $rows = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);

        if (empty($rows) || !isset($rows[0]['password'])) {
            return 'User not found.';
        }

        $hash = $rows[0]['password'];

        if (!password_verify($currentPassword, $hash)) {
            return 'Current password is incorrect.';
        }

        // Hash and update
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql2 = "UPDATE users SET password = ? WHERE id = ?";
        $stmt2 = $this->db->executeQuery($sql2, [$newHash, (string)$userId]);
        mysqli_stmt_close($stmt2);

        return true;
    }

    function createPasswordResetToken($email, $expiryMinutes = 60) {
        $user = $this->findByEmail($email);
        if (!$user) return false;

        $token = bin2hex(random_bytes(24));

        // Use DB time to set expiry so verify uses the same clock
        $expiryMinutes = (int)$expiryMinutes;
        $sql = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL $expiryMinutes MINUTE))";
        $params = [$user['id'], $token];
        $stmt = $this->db->executeQuery($sql, $params);
        mysqli_stmt_close($stmt);

        return $token;
    }

    /**
     * Verify token; returns user row if valid and not expired, otherwise false.
     */
    function verifyPasswordResetToken($token) {
        $sql = "SELECT pr.*, u.* FROM password_resets pr
                JOIN users u ON pr.user_id = u.id
                WHERE pr.token = ? AND pr.expires_at >= NOW()
                LIMIT 1";
        $stmt = $this->db->executeQuery($sql, [$token]);
        $rows = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        return $rows[0] ?? false;
    }

    /**
     * Reset password using token. Returns true on success.
     */
    function resetPasswordByToken($token, $newPassword) {
        $entry = $this->verifyPasswordResetToken($token);
        if (!$entry) return false;

        $userId = $entry['user_id'];
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, [$hash, $userId]);
        mysqli_stmt_close($stmt);

        // delete all existing tokens for this user
        $sql2 = "DELETE FROM password_resets WHERE user_id = ?";
        $stmt2 = $this->db->executeQuery($sql2, [$userId]);
        mysqli_stmt_close($stmt2);

        return true;
    }

    // --- New OTP methods for SMS/email OTP flow ---

    /**
     * Create an expiring numeric OTP for password reset (stored in password_resets.token).
     * Returns the OTP string on success, false on failure or if email not found.
     */
    function createPasswordResetOTP($email, $expiryMinutes = 15, $length = 6) {
        $user = $this->findByEmail($email);
        if (!$user) return false;

        $length = max(4, min(8, (int)$length));
        $min = (int) pow(10, $length - 1);
        $max = (int) pow(10, $length) - 1;

        try {
            $otp = (string) random_int($min, $max);
        } catch (Exception $e) {
            return false;
        }

        $expiryMinutes = (int)$expiryMinutes;
        
        // --- CHANGE: Delete previous tokens for this user first ---
        $delSql = "DELETE FROM password_resets WHERE user_id = ?";
        $delStmt = $this->db->executeQuery($delSql, [(string)$user['id']]);
        mysqli_stmt_close($delStmt);
        // ----------------------------------------------------------

        // reuse password_resets table — token column will hold numeric OTP
        $sql = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL $expiryMinutes MINUTE))";
        $params = [$user['id'], $otp];
        $stmt = $this->db->executeQuery($sql, $params);
        mysqli_stmt_close($stmt);

        return $otp;
    }

    /**
     * Verify OTP for a user by email and OTP value; returns the password_resets row (with user info) on success, false otherwise.
     */
    function verifyPasswordResetOTP($email, $otp) {
        $user = $this->findByEmail($email);
        if (!$user) return false;

        $sql = "SELECT pr.*, u.* FROM password_resets pr
                JOIN users u ON pr.user_id = u.id
                WHERE pr.token = ? AND pr.user_id = ? AND pr.expires_at >= NOW()
                LIMIT 1";
        $stmt = $this->db->executeQuery($sql, [$otp, $user['id']]);
        $rows = $this->db->fetch($stmt);
        mysqli_stmt_close($stmt);
        return $rows[0] ?? false;
    }

    /**
     * Reset password directly by user id (used when OTP has been verified and session holds user id).
     * Returns true on success.
     */
    function resetPasswordForUser($userId, $newPassword) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, [$hash, (string)$userId]);
        mysqli_stmt_close($stmt);

        // delete existing tokens/OTPs for this user
        $sql2 = "DELETE FROM password_resets WHERE user_id = ?";
        $stmt2 = $this->db->executeQuery($sql2, [(string)$userId]);
        mysqli_stmt_close($stmt2);

        return true;
    }
}
?>