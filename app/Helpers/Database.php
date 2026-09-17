<?php

namespace App\Helpers;

use PDO;
use PDOException;
use Exception;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            Config::loadEnv();

            $driver = Config::get('DB_DRIVER', 'mysql');
            $host = Config::get('DB_HOST', '127.0.0.1');
            $port = Config::get('DB_PORT', '3306');
            $db = Config::get('DB_DATABASE', 'puno_at_halaman');
            $user = Config::get('DB_USERNAME', 'root');
            $pass = Config::get('DB_PASSWORD', '');

            if ($driver === 'sqlite') {
                try {
                    $dsn = "sqlite:" . $db;
                    self::$instance = new PDO($dsn, null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                    return self::$instance;
                } catch (PDOException $e) {
                    throw new Exception("SQLite Connection Failed: " . $e->getMessage());
                }
            }

            // Connection credentials to attempt
            $credentialSets = [
                ['host' => $host, 'user' => $user, 'pass' => $pass],
            ];

            // If primary host is 127.0.0.1/localhost, try alternate hostname
            if ($host === '127.0.0.1') {
                $credentialSets[] = ['host' => 'localhost', 'user' => $user, 'pass' => $pass];
            } elseif ($host === 'localhost') {
                $credentialSets[] = ['host' => '127.0.0.1', 'user' => $user, 'pass' => $pass];
            }

            // Fallback for XAMPP environments if custom user access fails
            if ($user !== 'root') {
                $credentialSets[] = ['host' => $host, 'user' => 'root', 'pass' => ''];
                $credentialSets[] = ['host' => ($host === '127.0.0.1' ? 'localhost' : '127.0.0.1'), 'user' => 'root', 'pass' => ''];
            }

            $lastException = null;
            foreach ($credentialSets as $cred) {
                try {
                    $dsn = "mysql:host={$cred['host']};port={$port};dbname={$db};charset=utf8mb4";
                    self::$instance = new PDO($dsn, $cred['user'], $cred['pass'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                    return self::$instance;
                } catch (PDOException $e) {
                    $lastException = $e;
                }
            }

            error_log("Database connection error: " . ($lastException ? $lastException->getMessage() : "Unknown"));
            throw new Exception("Database connection failed. Please check your configuration. (" . ($lastException ? $lastException->getMessage() : "") . ")");
        }

        return self::$instance;
    }
}
