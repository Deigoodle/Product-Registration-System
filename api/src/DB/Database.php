<?php
declare(strict_types=1);

namespace App\DB;

use PDO;
use PDOException;

class Database {
    private ?string $dsn = null;
    private ?string $username = null;
    private ?string $password = null;
    private static ?self $instance = null;

    private function __construct()
    {
    }

    public function connect(string $dsn, string $username, string $password): PDO
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;

        try {
            return $this->getConnection();
        } catch (PDOException $e) {
            throw new PDOException('Database connection failed: ' . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    public function getConnection(): PDO
    {
        return new PDO(
            $this->dsn,
            $this->username,
            $this->password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
