<?php

namespace App\Middleware;

use Owi\middleware\Middleware;

class CorsMiddleware implements Middleware
{
    public function handle($request, callable $next)
    {
        $allowed_origin = getenv("ALLOW_ORIGIN") ?: "*";

        header("Access-Control-Allow-Origin: $allowed_origin");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
             header("HTTP/1.1 200 OK");
             return;
        }

        return $next($request);
    }
}
