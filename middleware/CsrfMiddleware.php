<?php

namespace App\middleware;

use Exception;

class CsrfMiddleware implements Middleware
{
    public function handle($request, callable $next)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && 
            $_SERVER['REQUEST_METHOD'] !== 'PUT' && 
            $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            return $next($request);
        }

        // Check for token in POST data or Headers
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!$token || $token !== csrf_token()) {
            http_response_code(419);
            echo json_encode(['error' => 'Page Expired (CSRF Token Mismatch)']);
            return;
        }

        return $next($request);
    }
}
