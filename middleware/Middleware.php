<?php

namespace App\middleware;

interface Middleware
{
    public function handle($request, callable $next);
}
