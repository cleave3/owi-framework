<?php

namespace Owi\router;

class Router
{
    protected $container;

    public function __construct(?\Owi\core\Container $container = null)
    {
        $this->container = $container ?: new \Owi\core\Container();
    }

    public function resolve_route()
    {
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $method = $_SERVER["REQUEST_METHOD"];

        // 1. Check explicit routes from Route class
        if ($this->handleExplicitRoutes($uri, $method)) {
            return;
        }

        // 2. Legacy/Auto API support (DEPRECATED but kept for now or repurposed)
        // If it starts with /api/, we can strictly say 404 if not found in explicit routes?
        // Or keep the old magic routing for backward compat?
        // User asked to "update the app router so that we can have an Routes folder... immitate express.js".
        // Use implicit "auto" routing only if explicit failed.

        $route_parameters = preg_split('/(\?|\/)/', $uri);

        // This block is the old behavior.
        if (isset($route_parameters[1]) && $route_parameters[1] === "api" && count($route_parameters) >= 3) {
             $classprefix = $route_parameters[2];
             $class_method = count($route_parameters) > 3 ?
                 (empty($route_parameters[3]) ? "index"
                     : $route_parameters[3]) : "index";
             $param = count($route_parameters) == 5 ? $route_parameters[4] : null;
 
             $this->runapi(
                 $uri,
                 "App\Controllers\\" . ucwords($classprefix) . "Controller",
                 $class_method,
                 $param
             );
        } else {
             $this->runweb($uri);
        }
    }

    protected function handleExplicitRoutes($uri, $method)
    {
        $routes = Route::getRoutes();
        $candidates = $routes[$method] ?? [];
        if (isset($routes['ANY'])) {
            $candidates = array_merge($candidates, $routes['ANY']);
        }

        foreach ($candidates as $routePath => $definition) {
            // Convert route path to regex: /user/{id} -> #^/user/([^/]+)$#
            // Escape slashes
            $pattern = preg_quote($routePath, '#');
            // Replace {param} with regex capture group
            $pattern = preg_replace('/\\\{[a-zA-Z0-9_]+\\\}/', '([^/]+)', $pattern);
            // Add start/end delimiters
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                
                $callback = $definition['callback'];
                $middlewares = $definition['middleware'] ?? [];

                // Execute
                $this->executeRoute($callback, $matches, $middlewares);
                return true;
            }
        }

        return false;
    }

    protected function executeRoute($callback, $params, $middlewares)
    {
        // 1. Run Middlewares
        // We need to resolve middleware instances from class strings if needed
        $concreteMiddlewares = [];
        foreach ($middlewares as $mw) {
            if (is_string($mw) && class_exists($mw)) {
                $concreteMiddlewares[] = new $mw;
            } elseif (is_object($mw)) {
                $concreteMiddlewares[] = $mw;
            }
        }

        (new \Owi\core\Pipeline())
            ->send($_SERVER) // Pass request info
            ->through($concreteMiddlewares)
            ->then(function ($request) use ($callback, $params) {
                // 2. Execute Callback
                if (is_callable($callback)) {
                    echo call_user_func_array($callback, $params);
                } elseif (is_string($callback)) {
                    // "Controller@method" or "Controller" (default index)
                    $parts = explode('@', $callback);
                    $controllerClass = $parts[0];
                    $method = $parts[1] ?? 'index';

                    // If user passed short name "UserController", assume App\Controllers\
                    if (!class_exists($controllerClass)) {
                        $namespaced = "App\\Controllers\\" . $controllerClass;
                        if (class_exists($namespaced)) {
                            $controllerClass = $namespaced;
                        }
                    }

                    if (class_exists($controllerClass)) {
                        try {
                            $controller = $this->container->get($controllerClass);
                            echo call_user_func_array([$controller, $method], $params);
                        } catch (\Exception $e) {
                            http_response_code(500);
                            echo "Error resolving controller: " . $e->getMessage();
                        }
                    } else {
                        http_response_code(500);
                        echo "Controller not found: $controllerClass";
                    }
                } else {
                    if (is_array($callback) && count($callback) === 2) {
                         // [Controller::class, 'method']
                         $controllerClass = $callback[0];
                         $method = $callback[1];
                         try {
                            // If controllerClass is an object, use it directly? 
                            // Usually it's a class string.
                            $controller = is_string($controllerClass) ? $this->container->get($controllerClass) : $controllerClass;
                            echo call_user_func_array([$controller, $method], $params);
                        } catch (\Exception $e) {
                             http_response_code(500);
                             echo "Error resolving controller: " . $e->getMessage();
                        }
                    }
                }
            });
    }

    public function runapi($request, $class, $method, $param)
    {
        if ($request == "/") {
            echo json_encode(["status" => true, "code" => 200, "message" => "App is live"]);
            return;
        }

        if (class_exists($class)) {
            // Use Container to resolve the controller instance
            try {
                $instance = $this->container->get($class);
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => false, "code" => 500, "message" => $e->getMessage()]);
                return;
            }

            if ($instance instanceof \App\Controllers\Controller) {
                if (method_exists($instance, $method)) {
                    $middlewares = $instance->getMiddlewares();
                    
                    (new \Owi\core\Pipeline())
                        ->send($request)
                        ->through($middlewares)
                        ->then(function ($request) use ($instance, $method, $param) {
                            echo $instance->$method($param);
                        });
                    return;
                }
            }
        }
        
        http_response_code(404);
        echo json_encode(["status" => false, "code" => 404, "message" => "Route or resource not found"]);
    }

    public function runweb($path)
    {
        $file_to_load = $path;

        if (preg_match_all('/(\?)/',  $path)) $file_to_load = explode("?", $path)[0];
        
        // Remove trailing slash
        $file_to_load = rtrim($file_to_load, '/');

        if (empty($file_to_load)) $file_to_load = "index";
        if ($file_to_load == "/") $file_to_load = "index";
        
        // Normalize
        if(substr($file_to_load, 0, 1) === '/') $file_to_load = substr($file_to_load, 1);


        $baseViewPath = realpath(__DIR__ . "/../../src/Views");
        
        // Check exact match
        $file = $baseViewPath . "/" . $file_to_load . ".php";
        
        // Check index inside folder
        if (!file_exists($file)) {
            $file = $baseViewPath . "/" . $file_to_load . "/index.php";
        }

        // Try just the folder (implies index if strict logic wasn't there)
        
        if (!file_exists($file)) {
             // 404 Page
             $file = $baseViewPath . "/404.php";
             if (file_exists($file)) {
                 http_response_code(404);
             } else {
                 // Fallback if no custom 404
                 http_response_code(404);
                 echo "404 Not Found";
                 return;
             }
        }

        require $file;
    }

    public function run()
    {
        $this->resolve_route();
    }
}
