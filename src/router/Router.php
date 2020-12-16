<?php

namespace App\router;

use App\utils\Response;

class Router
{
    public $request;
    public $response;

    public function __construct($request)
    {
        $this->request = $request;
        $this->response = new Response();
    }

    public function run()
    {
        $route_parameters = explode("/", $this->request);
        $classprefix = $route_parameters[1];
        $class_method = explode("?", $route_parameters[2])[0];

        $class = "App\controllers\\" . ucwords($classprefix) . "Controller";

        if ($this->request == "/") {
            echo $this->response->success("App is live");
        } else if (class_exists($class)) {
            $instance = new $class();
            echo count($route_parameters) == 2 ? $instance->index() : $instance->$class_method();
        } else {
            echo $this->response->badRequest("route not found");
        }
    }
}
