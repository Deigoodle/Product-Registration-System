<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Producto;
use App\Validator\ProductoValidator;
use App\DB\Database;
use PDO;
use PDOException;

class ProductoService {
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function initializeDatabaseConnection(string $dsn, string $username, string $password): void
    {
        $this->db->connect($dsn, $username, $password);
    }

    public function create(array $data): array
    {
        try {
            $producto = new Producto($data);
        } catch (\InvalidArgumentException $e) {
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }

        try {
            $pdo = $this->db->getConnection();
            $existingMaterialIds = array_map(
                'intval',
                $pdo->query('SELECT id FROM materiales')->fetchAll(PDO::FETCH_COLUMN)
            );
        } catch (PDOException $e) {
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }

        $validationErrors = ProductoValidator::validate($producto, $existingMaterialIds);
        if ($validationErrors !== []) {
            return ['success' => false, 'errors' => $validationErrors];
        }

        try {
            $pdo = $this->db->getConnection();
            $pdo->beginTransaction();

            $statement = $pdo->prepare('
                INSERT INTO productos (codigo, nombre, descripcion, precio, moneda_id, bodega_id, sucursal_id, material_id)
                VALUES (:codigo, :nombre, :descripcion, :precio, :moneda_id, :bodega_id, :sucursal_id, :material_id)
                RETURNING id
            ');
            $statement->execute([
                ':codigo' => $producto->codigo,
                ':nombre' => $producto->nombre,
                ':descripcion' => $producto->descripcion,
                ':precio' => $producto->precio,
                ':moneda_id' => $producto->moneda_id,
                ':bodega_id' => $producto->bodega_id,
                ':sucursal_id' => $producto->sucursal_id,
                ':material_id' => $producto->materiales[0],
            ]);

            $productId = (int) $statement->fetchColumn();

            $link = $pdo->prepare('
                INSERT INTO productos_materiales (producto_id, material_id)
                VALUES (:producto_id, :material_id)
            ');
            foreach ($producto->materiales as $materialId) {
                $link->execute([
                    ':producto_id' => $productId,
                    ':material_id' => $materialId,
                ]);
            }

            $pdo->commit();
            $producto->id = $productId;

            return ['success' => true, 'data' => $producto->toArray()];
        } catch (PDOException $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }
}
