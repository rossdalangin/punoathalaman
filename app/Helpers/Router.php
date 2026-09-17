<?php

namespace App\Helpers;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function patch(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('PATCH', $path, $handler, $middleware);
    }

    public function delete(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, callable|array $handler, array $middleware = []): void
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[^/]+)', $path);
        $pattern = "#^" . $pattern . "$#";

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $parsedUrl = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Base path strip for subdirectories (e.g. /punoathalaman/public)
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($scriptDir !== '/' && str_starts_with($parsedUrl, $scriptDir)) {
            $path = substr($parsedUrl, strlen($scriptDir));
        } else {
            $path = $parsedUrl;
        }

        $path = rtrim($path, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] === strtoupper($method) && preg_match($route['pattern'], $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Run middleware
                foreach ($route['middleware'] as $mwClass) {
                    $middleware = new $mwClass();
                    if (!$middleware->handle()) {
                        return;
                    }
                }

                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$class, $methodName] = $handler;
                    $controller = new $class();
                    call_user_func_array([$controller, $methodName], $params);
                } else {
                    call_user_func_array($handler, $params);
                }
                return;
            }
        }

        // 404 Not Found
        if (str_contains($path, '/api/')) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found', 'path' => $path]);
        } else {
            http_response_code(404);
            echo "<h1>404 - Pahina Hindi Natagpuan (Page Not Found)</h1>";
        }
    }
}
