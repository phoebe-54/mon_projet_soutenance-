<?php

declare(strict_types=1);

if (!class_exists('Database')) {
    final class Database
    {
        private const HOST = 'localhost';
        private const DB_NAME = 'gestion_nocibe';
        private const USERNAME = 'root';
        private const PASSWORD = '';

        private static ?PDO $connection = null;

        public static function getConnection(): PDO
        {
            if (self::$connection === null) {
                self::$connection = new PDO(
                    'mysql:host=' . self::HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4',
                    self::USERNAME,
                    self::PASSWORD,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            }

            return self::$connection;
        }
    }
}

$pdo = Database::getConnection();
