<?php
class Auth
{

    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function isLoggedIn()
    {
        self::start();
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    public static function requireAuth()
    {
        if (!self::isLoggedIn()) {
            header("Location:/PackIT/frontend/login.php");
            exit();
        }
    }

    public static function requireAdmin()
    {
        if (!self::isLoggedIn() || !self::isAdmin()) {
            header("Location:/PackIT/admin/index.php");
            exit();
        }
    }

    public static function requireDriver()
    {
        if (!self::isLoggedIn() || !self::isDriver()) {
            header("Location:/PackIT/driver/login.php");
            exit();
        }
    }

    public static function redirectIfLoggedIn()
    {
        if (self::isLoggedIn()) {
            if (self::isAdmin()) {
                header("Location:/PackIT/admin/dashboard.php");
            } elseif (self::isDriver()) {
                header("Location:/PackIT/driver/dashboard.php");
            } else {
                header("Location:/PackIT/frontend/profile.php");
            }
            exit();
        }
    }

    public static function login($userId, $email, $name, $role = 'user')
    {
        self::start();
        
        // Extract first and last name from full name
        $nameParts = explode(' ', trim($name), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';
        
        // Set structured user session array (for navbar and other components)
        $_SESSION['user'] = [
            'id'        => $userId,
            'email'     => $email,
            'firstName' => $firstName,
            'lastName'  => $lastName,
            'role'      => $role
        ];
        
        // Also set individual keys for backward compatibility
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = $role;
    }

    public static function logout()
    {
        self::start();
        session_unset();
        session_destroy();
    }

    public static function getUser()
    {
        if (!  self::isLoggedIn()) {
            return null;
        }
        return [
            'id' => $_SESSION['user']['id'] ?? null,
            'email' => $_SESSION['user']['email'] ?? '',
            'firstName' => $_SESSION['user']['firstName'] ?? '',
            'lastName' => $_SESSION['user']['lastName'] ?? '',
            'name' => $_SESSION['user']['firstName'] .' ' .$_SESSION['user']['lastName'],
            'role' => $_SESSION['user']['role'] ?? 'user'
        ];
    }

    public static function isAdmin()
    {
        self::start();
        return isset($_SESSION['user']) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
    }

    public static function isDriver()
    {
        self::start();
        return isset($_SESSION['user']) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'driver';
    }

    public static function redirectIfAdminLoggedIn()
    {
        self::start();
        if (self::isLoggedIn() && self::isAdmin()) {
            header("Location:/PackIT/admin/dashboard.php");
            exit();
        }
    }
}