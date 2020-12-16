<?php

use App\router\Router;

require __DIR__ . '/../vendor/autoload.php';

$request = $_SERVER["REQUEST_URI"];

$router = new Router($request);
$router->run();
