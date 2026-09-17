<?php

/** @var App\Helpers\Router $router */

// AI Identification
$router->post('/api/identify', [App\Controllers\Api\IdentifyController::class, 'identify']);
$router->post('/api/identify/multiple', [App\Controllers\Api\IdentifyController::class, 'identifyMultiple']);

// Plant Database
$router->get('/api/plants', [App\Controllers\Api\PlantApiController::class, 'index']);
$router->get('/api/plants/search', [App\Controllers\Api\PlantApiController::class, 'search']);
$router->get('/api/plants/{id}', [App\Controllers\Api\PlantApiController::class, 'show']);

// Field Observations
$router->get('/api/observations', [App\Controllers\Api\ObservationApiController::class, 'index']);
$router->post('/api/observations', [App\Controllers\Api\ObservationApiController::class, 'store']);
$router->get('/api/observations/export', [App\Controllers\Api\ObservationApiController::class, 'export']);

// Expert Review
$router->post('/api/expert-review', [App\Controllers\Api\ExpertReviewApiController::class, 'store']);

// Sources
$router->get('/api/sources', [App\Controllers\Api\SourceApiController::class, 'index']);

// Auth
$router->post('/api/auth/login', [App\Controllers\Api\AuthApiController::class, 'login']);
$router->post('/api/auth/logout', [App\Controllers\Api\AuthApiController::class, 'logout']);

// Admin endpoints
$router->get('/api/admin/stats', [App\Controllers\Api\AdminApiController::class, 'getStats']);
$router->get('/api/admin/reviews', [App\Controllers\Api\AdminApiController::class, 'getReviews']);
$router->post('/api/admin/reviews/verify', [App\Controllers\Api\AdminApiController::class, 'verifyReview']);
$router->post('/api/admin/plants', [App\Controllers\Api\AdminApiController::class, 'storePlant'], [App\Middleware\AuthMiddleware::class]);
$router->patch('/api/admin/plants/{id}', [App\Controllers\Api\AdminApiController::class, 'updatePlant'], [App\Middleware\AuthMiddleware::class]);
