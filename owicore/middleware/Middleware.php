<?php

namespace Owi\middleware;

interface Middleware
{
    public function handle($request, callable $next);
}
