<?php
declare(strict_types=1);

namespace App\DB;

use PDO;
use PDOException;

class Database {
    private static ?self $instance = null;
    private ?PDO $pdo = null;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function connect(string $dsn, string $username, string $password): void
    {
        if ($this->pdo !== null) {
            return;
        }

        try {
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (PDOException $e) {
            throw new PDOException('Database connection failed: ' . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    public function getConnection(): PDO
    {
        if ($this->pdo === null) {
            throw new PDOException('Database not connected. Call connect() first.');
        }
        return $this->pdo;
    }
}
