<?php

namespace middleware\Auth;

use Firebase\JWT\JWT;

class Auth
{
    protected $token;
    protected $secret;

    public function __construct()
    {
        $this->token = $_SERVER["HTTP_TOKEN"];
        $this->secret = getenv("JWT_SECRET");
    }

    public $authmethods = [];

    public function useAuth($methods)
    {
    }

    public function excempt($methods)
    {
    }

    public function checkAuth()
    {
    }
}
