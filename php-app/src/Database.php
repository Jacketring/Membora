<?php

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $config = require __DIR__ . '/../config/config.php';
        $url = $config['database_url'];

        if (!$url && !empty($config['database']['database'])) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $config['database']['host'],
                $config['database']['port'],
                $config['database']['database']
            );

            self::$pdo = new PDO($dsn, $config['database']['username'], $config['database']['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            return self::$pdo;
        }

        if (!$url) {
            throw new RuntimeException('No hay conexion de base de datos configurada.');
        }

        $parts = parse_url($url);
        if (!$parts || empty($parts['host']) || empty($parts['path'])) {
            throw new RuntimeException('DATABASE_URL no valida. Usa DB_HOST, DB_DATABASE, DB_USERNAME y DB_PASSWORD si la clave tiene caracteres especiales.');
        }

        $database = ltrim($parts['path'], '/');
        $port = $parts['port'] ?? 3306;
        $username = urldecode($parts['user'] ?? '');
        $password = urldecode($parts['pass'] ?? '');
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $parts['host'], $port, $database);

        self::$pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return self::$pdo;
    }
}
