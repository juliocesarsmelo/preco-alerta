<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

class Database {

    private static $pdo;

    public static function conectar() {

        if (!self::$pdo) {

            // carregar .env
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();

            try {
                self::$pdo = new \PDO(
                    "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
                    $_ENV['DB_USER'],
                    $_ENV['DB_PASS']
                );

                self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
                self::$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

            } catch (\PDOException $e) {
                die("Erro na conexão: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}