<?php
error_reporting(1);

require __DIR__ . '/vendor/autoload.php';

use App\router\Router;
use App\config\DotEnv;
use App\core\Container;

require_once __DIR__ . '/utils/SecurityUtils.php';

(new DotEnv(__DIR__ . '/.env'))->load();

new \App\core\ExceptionHandler();

$container = new Container();
$router = new Router($container);
$router->run();
