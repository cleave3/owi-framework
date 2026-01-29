<?php

namespace Owi\core;

class Pipeline
{
    private $request;
    private $middlewares = [];
    private $action;

    public function send($request)
    {
        $this->request = $request;
        return $this;
    }

    public function through(array $middlewares)
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    public function then(callable $action)
    {
        $this->action = $action;
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            function ($next, $middleware) {
                return function ($request) use ($next, $middleware) {
                    $instance = is_string($middleware) ? new $middleware() : $middleware;
                    return $instance->handle($request, $next);
                };
            },
            $this->action
        );

        return $pipeline($this->request);
    }
}
