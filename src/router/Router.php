<?php

namespace App\router;

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
        $param = count($route_parameters) == 4 ? $route_parameters[3] : null;

        return [
            $request,
            "App\controllers\\" . ucwords($classprefix) . "Controller",
            $class_method,
            $param
        ];
    }

    public static function run()
    {

        list($request, $class, $method, $param) = self::resolve_route();
        if ($request == "/") {
            echo json_encode(["status" => true, "message" => "App is live"]);
        } else {

            if (method_exists($class, $method)) {
                $instance = new $class();
                echo $instance->$method($param);
            } else {
                echo json_encode(["status" => 404, "message" => "Route not found"]);
            }
        }
    }
}
