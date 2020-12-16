<?php

namespace App\utils;

class Session
{
    public function __construct()
    {
        session_start();
    }

    public static function set(array $var)
    {
        foreach ($var as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }

    public static function get($key)
    {
        return $_SESSION[$key];
    }

    public static function destroy()
    {
        session_start();
        session_destroy();
    }
}
