<?php

// Check Composer autoloader or load fallback autoloader
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    require_once __DIR__ . '/../app/Helpers/Autoloader.php';
    \App\Helpers\Autoloader::register();
}

use App\Helpers\Config;
use App\Helpers\Router;

// Check if application is installed
$lockFile = __DIR__ . '/../storage/installed.lock';
if (!file_exists($lockFile) && file_exists(__DIR__ . '/install.php')) {
    header('Location: install.php');
    exit;
}

// Load Environment Configuration
Config::loadEnv(__DIR__ . '/../.env');

// Start PHP Session securely
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

// Instantiate Router and load routes
$router = new Router();

require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';

// Dispatch Request
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

$router->dispatch($method, $uri);
