<?php

namespace App\Helpers;

class View
{
    public static function render(string $viewPath, array $data = []): void
    {
        extract($data);
        $fullPath = __DIR__ . '/../../views/' . ltrim($viewPath, '/') . '.php';

        if (file_exists($fullPath)) {
            require $fullPath;
        } else {
            http_response_code(500);
            echo "View file not found: {$viewPath}";
        }
    }
}
