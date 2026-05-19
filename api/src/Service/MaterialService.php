<?php
declare(strict_types=1);

namespace App\Service;

use App\DB\Database;
use App\Model\Material;
use PDO;
use PDOException;

class MaterialService {
    private PDO $pdo;

    public function __construct(string $dsn, string $username, string $password) {
        $db = Database::getInstance();
        $db->connect($dsn, $username, $password);
        $this->pdo = $db->getConnection();
    }

    public function findAll(): array {
        try {
            $rows = $this->pdo->query('SELECT * FROM materiales ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
            $data = array_map(
                fn (array $row) => Material::fromRow($row)->toArray(),
                $rows
            );

            return ['success' => true, 'data' => $data];
        } catch (PDOException $e) {
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }

    public function find(int $id): array {
        $row = $this->fetchRow($id);
        if ($row === null) {
            return ['success' => false, 'errors' => ['Material no encontrado']];
        }

        return [
            'success' => true,
            'data' => Material::fromRow($row)->toArray(),
        ];
    }

    private function fetchRow(int $id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM materiales WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? $row : null;
    }
}
