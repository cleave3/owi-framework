<?php

namespace App\core;

use App\utils\DB;

class Auth
{
    /**
     * Attempt to login a user
     * 
     * @param array $credentials ['email' => '...', 'password' => '...']
     * @return bool
     */
    public static function attempt(array $credentials)
    {
        $email = $credentials['email'] ?? null;
        $password = $credentials['password'] ?? null;

        if (!$email || !$password) {
            return false;
        }

        $user = DB::table('users')->where('email', $email)->first();

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['password'])) {
            self::login($user);
            return true;
        }

        return false;
    }

    /**
     * Log in a user by ID or data
     * 
     * @param mixed $user
     */
    public static function login($user)
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        // Store minimal user info in session
        $_SESSION['user_id'] = $user['id'] ?? $user;
    }

    /**
     * Log out the current user
     */
    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
        
        unset($_SESSION['user_id']);
        session_destroy();
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public static function check()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    /**
     * Get authenticated user
     * 
     * @return mixed|null
     */
    public static function user()
    {
        if (!self::check()) {
            return null;
        }

        return DB::table('users')->where('id', $_SESSION['user_id'])->first();
    }

    /**
     * Get user ID
     * 
     * @return int|null
     */
    public static function id()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
        return $_SESSION['user_id'] ?? null;
    }
}
