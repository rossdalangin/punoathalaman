<?php

namespace App\Middleware;

use App\Helpers\Config;

class AuthMiddleware
{
    public function handle(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized access. Please login first.']);
            } else {
                header('Location: /login');
            }
            return false;
        }

        return true;
    }
}
