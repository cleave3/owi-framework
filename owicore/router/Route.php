<?php

namespace Owi\router;

class Route
{
    /**
     * @var array $routes Stores all registered routes
     */
    protected static $routes = [];

    /**
     * Register a GET route
     * 
     * @param string $path
     * @param mixed $callback
     * @param array $middleware
     */
    public static function get($path, $callback, $middleware = [])
    {
        self::addRoute('GET', $path, $callback, $middleware);
    }

    /**
     * Register a POST route
     * 
     * @param string $path
     * @param mixed $callback
     * @param array $middleware
     */
    public static function post($path, $callback, $middleware = [])
    {
        self::addRoute('POST', $path, $callback, $middleware);
    }

    /**
     * Register a PUT route
     * 
     * @param string $path
     * @param mixed $callback
     * @param array $middleware
     */
    public static function put($path, $callback, $middleware = [])
    {
        self::addRoute('PUT', $path, $callback, $middleware);
    }

    /**
     * Register a DELETE route
     * 
     * @param string $path
     * @param mixed $callback
     * @param array $middleware
     */
    public static function delete($path, $callback, $middleware = [])
    {
        self::addRoute('DELETE', $path, $callback, $middleware);
    }

    /**
     * Register a route for any method
     * 
     * @param string $path
     * @param mixed $callback
     * @param array $middleware
     */
    public static function any($path, $callback, $middleware = [])
    {
        self::addRoute('ANY', $path, $callback, $middleware);
    }

    /**
     * Internal method to add route to static store
     */
    protected static function addRoute($method, $path, $callback, $middleware)
    {
        // Ensure path starts with /
        if ($path !== '/' && substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }
        
        // Remove trailing slash if not root
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }

        self::$routes[$method][$path] = [
            'callback' => $callback,
            'middleware' => $middleware
        ];
    }

    /**
     * Get all registered routes
     * 
     * @return array
     */
    public static function getRoutes()
    {
        return self::$routes;
    }
}
