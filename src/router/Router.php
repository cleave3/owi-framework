<?php

namespace App\router;

use const App\config\APP_NAME;

class Router
{
    public static function resolve_route()
    {
        $request = $_SERVER["REQUEST_URI"];
        $route_parameters = preg_split('/(\?|\/)/', $request);
        $classprefix = $route_parameters[1];

        $class_method = count($route_parameters) > 2 ?
            (empty($route_parameters[2]) ? "index"
                : $route_parameters[2]) : "index";

        return [
            $request,
            "App\controllers\\" . ucwords($classprefix) . "Controller",
            $class_method
        ];
    }

    public static function run()
    {
        list($request, $class, $method) = self::resolve_route();
        if ($request == "/") {
            echo json_encode(["status" => true, "message" => APP_NAME . " is live"]);
        } else {

            if (class_exists($class)) {
                $instance = new $class();
                echo $instance->$method();
            } else {
                echo json_encode(["status" => 404, "message" => "Route not found"]);
            }
        }
    }
}
