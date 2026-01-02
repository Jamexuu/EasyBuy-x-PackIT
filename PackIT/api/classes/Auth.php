<?php
class Auth {
    
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function isLoggedIn() {
        self::start();
        return isset($_SESSION['user_id']);
    }
    
    public static function requireAuth() {
        if (!self:: isLoggedIn()) {
            header("Location: /PackIT/frontend/login.php");
            exit();
        }
    }
    
    public static function requireAdmin() {
        if (!self:: isLoggedIn() || !self::isAdmin()) {
            header("Location: /PackIT/admin/index.php");
            exit();
        }
    }

    public static function requireDriver() {
        if (!self:: isLoggedIn() || !self::isDriver()) {
            header("Location: /PackIT/driver/login.php");
            exit();
        }
    }
    
    public static function redirectIfLoggedIn() {
        if (self::isLoggedIn()) {
            if (self::isAdmin()) {
                header("Location: /PackIT/admin/dashboard.php");
            } elseif (self::isDriver()) {
                header("Location: /PackIT/driver/dashboard.php");
            } else {
                header("Location: /PackIT/frontend/dashboard.php");
            }
            exit();
        }
    }
    
    public static function login($userId, $email, $name, $role = 'user') {
        self::start();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = $role;
    }
    
    public static function logout() {
        self::start();
        session_unset();
        session_destroy();
    }
    
    public static function getUser() {
        if (! self::isLoggedIn()) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'] ?? '',
            'name' => $_SESSION['user_name'] ??  'User',
            'role' => $_SESSION['user_role'] ?? 'user'
        ];
    }
    
    public static function isAdmin() {
        self::start();
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public static function isDriver() {
        self::start();
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'driver';
    }
}