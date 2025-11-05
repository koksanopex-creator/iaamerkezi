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

// 🚨 KRİTİK IIS ALT DİZİN FIX BAŞLANGICI 🚨
// Livewire ve tüm rotaların /iaa altından doğru çalışmasını sağlamak için.
$_SERVER['SCRIPT_NAME'] = '/iaa/index.php';
$_SERVER['PHP_SELF'] = '/iaa/index.php'; 
// 🚨 KRİTİK IIS ALT DİZİN FIX SONU 🚨

$app->handleRequest(Request::capture());
