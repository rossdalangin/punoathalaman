<?php

/** @var App\Helpers\Router $router */

$router->get('/', [App\Controllers\HomeController::class, 'index']);
$router->get('/educational', [App\Controllers\EducationalController::class, 'index']);
$router->get('/observations', [App\Controllers\ObservationController::class, 'index']);
$router->get('/admin', [App\Controllers\AdminController::class, 'index']);
$router->get('/login', [App\Controllers\AuthController::class, 'loginForm']);
