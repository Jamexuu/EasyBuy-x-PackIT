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
        if (!self::isLoggedIn()) {
            header("Location: /EasyBuy-x-PackIT/EasyBuy/frontend/login.php");
            exit();
        }
    }
    
    public static function redirectIfLoggedIn() {
        if (self::isLoggedIn()) {
            header("Location: /EasyBuy-x-PackIT/EasyBuy/frontend/account.php");
            exit();
        }
    }
    
    public static function login($userId, $email, $name) {
        self::start();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name;
    }
    
    public static function logout() {
        self::start();
        session_unset();
        session_destroy();
    }
    
    public static function getUser() {
        if (!self::isLoggedIn()) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'] ?? '',
            'name' => $_SESSION['user_name'] ?? 'User'
        ];
    }
}