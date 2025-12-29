<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Sanitize $_SERVER and $_GET from null bytes before capturing request
if (isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = str_replace("\0", '', $_SERVER['REQUEST_URI']);
}
if (isset($_SERVER['PATH_INFO'])) {
    $_SERVER['PATH_INFO'] = str_replace("\0", '', $_SERVER['PATH_INFO']);
}
if (isset($_GET)) {
    foreach ($_GET as $key => $value) {
        if (is_string($value)) {
            $_GET[$key] = str_replace("\0", '', $value);
        }
    }
}

$app->handleRequest(Request::capture());
