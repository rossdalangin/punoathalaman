<?php

use App\Helpers\Config;
use App\Helpers\Router;

require_once __DIR__ . '/../vendor/autoload.php';

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
