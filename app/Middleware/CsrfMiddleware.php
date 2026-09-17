<?php

namespace App\Middleware;

class CsrfMiddleware
{
    public static function generateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public function handle(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (in_array($requestMethod, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'CSRF token validation failed.']);
                return false;
            }
        }

        return true;
    }
}
