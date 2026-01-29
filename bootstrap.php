<?php
error_reporting(1);

if (PHP_VERSION_ID < 80000) {
    die("Owi Framework requires PHP 8.0 or higher. You are running " . PHP_VERSION . ".\n");
}

require __DIR__ . '/vendor/autoload.php';

use Owi\router\Router;
use Owi\config\DotEnv;
use Owi\core\Container;

require_once __DIR__ . '/owicore/utils/SecurityUtils.php';
require_once __DIR__ . '/owicore/utils/GlobalHelpers.php';

(new DotEnv(__DIR__ . '/.env'))->load();

new \Owi\core\ExceptionHandler();

$container = new Container();

// Load Routes
require_once __DIR__ . '/src/Routes/index.php';

$router = new Router($container);
$router->run();
