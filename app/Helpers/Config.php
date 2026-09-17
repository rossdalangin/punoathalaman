<?php

namespace App\Helpers;

class Config
{
    private static array $config = [];

    public static function loadEnv(string $path = ''): void
    {
        $searchPaths = array_filter([
            $path,
            __DIR__ . '/../../.env',
            __DIR__ . '/../.env',
            dirname($_SERVER['SCRIPT_FILENAME'] ?? '') . '/.env',
            dirname($_SERVER['SCRIPT_FILENAME'] ?? '') . '/../.env'
        ]);

        $envFileToLoad = null;
        foreach ($searchPaths as $p) {
            if ($p && file_exists($p)) {
                $envFileToLoad = $p;
                break;
            }
        }

        if (!$envFileToLoad) {
            return;
        }

        $lines = file($envFileToLoad, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (empty($_ENV)) {
            self::loadEnv();
        }

        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        $envVal = getenv($key);
        if ($envVal !== false) {
            return $envVal;
        }

        return $default;
    }
}
