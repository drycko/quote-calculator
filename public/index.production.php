<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Custom maintenance mode check
$customMaintenance = false; // Set to true to enable maintenance mode

if ($customMaintenance) {
    require __DIR__.'/../laravel_deploy/resources/views/maintenance.blade.php';
    exit;
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../laravel_deploy/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../laravel_deploy/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../laravel_deploy/bootstrap/app.php';
$app->handleRequest(Request::capture());

