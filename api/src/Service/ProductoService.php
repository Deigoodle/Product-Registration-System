<?php
declare(strict_types=1);

namespace App\Service;

use App\DB\Database;
use App\Model\Producto;
use App\Validator\ProductoValidator;
use PDO;
use PDOException;

class ProductoService {
    private PDO $pdo;

    public function __construct(string $dsn, string $username, string $password)
    {
        $db = Database::getInstance();
        $db->connect($dsn, $username, $password);
        $this->pdo = $db->getConnection();
    }

    public function findAll(): array
    {
        try {
            $rows = $this->pdo->query('SELECT * FROM productos ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
            $data = array_map(fn (array $row) => $this->formatProducto($row), $rows);

            return ['success' => true, 'data' => $data];
        } catch (PDOException $e) {
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }

    public function find(int $id): array
    {
        $row = $this->fetchRow($id);
        if ($row === null) {
            return ['success' => false, 'errors' => ['Producto no encontrado']];
        }

        return ['success' => true, 'data' => $this->formatProducto($row)];
    }

    public function create(array $data): array
    {
        try {
            $producto = new Producto($data);
        } catch (\InvalidArgumentException $e) {
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }

        $errors = $this->validateProducto($producto);
        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare('
                INSERT INTO productos (codigo, nombre, descripcion, precio, moneda_id, bodega_id, sucursal_id, material_id)
                VALUES (:codigo, :nombre, :descripcion, :precio, :moneda_id, :bodega_id, :sucursal_id, :material_id)
                RETURNING id
            ');
            $stmt->execute($this->productoParams($producto));
            $productId = (int) $stmt->fetchColumn();

            $this->syncMateriales($productId, $producto->materiales);
            $this->pdo->commit();

            $producto->id = $productId;
            return ['success' => true, 'data' => $producto->toArray()];
        } catch (PDOException $e) {
            $this->rollback();
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }

    public function update(int $id, array $data): array
    {
        if ($this->fetchRow($id) === null) {
            return ['success' => false, 'errors' => ['Producto no encontrado']];
        }

        $data['id'] = $id;

        try {
            $producto = new Producto($data);
        } catch (\InvalidArgumentException $e) {
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }

        $errors = $this->validateProducto($producto);
        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare('
                UPDATE productos
                SET codigo = :codigo,
                    nombre = :nombre,
                    descripcion = :descripcion,
                    precio = :precio,
                    moneda_id = :moneda_id,
                    bodega_id = :bodega_id,
                    sucursal_id = :sucursal_id,
                    material_id = :material_id,
                    fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE id = :id
            ');
            $stmt->execute([...$this->productoParams($producto), ':id' => $id]);

            $this->syncMateriales($id, $producto->materiales);
            $this->pdo->commit();

            $producto->id = $id;
            return ['success' => true, 'data' => $producto->toArray()];
        } catch (PDOException $e) {
            $this->rollback();
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }

    public function delete(int $id): array
    {
        if ($this->fetchRow($id) === null) {
            return ['success' => false, 'errors' => ['Producto no encontrado']];
        }

        try {
            $stmt = $this->pdo->prepare('DELETE FROM productos WHERE id = :id');
            $stmt->execute([':id' => $id]);

            return ['success' => true, 'message' => 'Producto eliminado'];
        } catch (PDOException $e) {
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }

    private function validateProducto(Producto $producto): array
    {
        $materialIds = array_map(
            'intval',
            $this->pdo->query('SELECT id FROM materiales')->fetchAll(PDO::FETCH_COLUMN)
        );

        return ProductoValidator::validate($producto, $materialIds);
    }

    private function fetchRow(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM productos WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? $row : null;
    }

    private function loadMateriales(int $productId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT material_id FROM productos_materiales WHERE producto_id = :id ORDER BY material_id'
        );
        $stmt->execute([':id' => $productId]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    private function formatProducto(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'codigo' => $row['codigo'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'precio' => (float) $row['precio'],
            'moneda_id' => (int) $row['moneda_id'],
            'bodega_id' => (int) $row['bodega_id'],
            'sucursal_id' => (int) $row['sucursal_id'],
            'materiales' => $this->loadMateriales((int) $row['id']),
        ];
    }

    private function productoParams(Producto $producto): array
    {
        return [
            ':codigo' => $producto->codigo,
            ':nombre' => $producto->nombre,
            ':descripcion' => $producto->descripcion,
            ':precio' => $producto->precio,
            ':moneda_id' => $producto->moneda_id,
            ':bodega_id' => $producto->bodega_id,
            ':sucursal_id' => $producto->sucursal_id,
            ':material_id' => $producto->materiales[0],
        ];
    }

    private function syncMateriales(int $productId, array $materialIds): void
    {
        $delete = $this->pdo->prepare('DELETE FROM productos_materiales WHERE producto_id = :id');
        $delete->execute([':id' => $productId]);

        $link = $this->pdo->prepare('
            INSERT INTO productos_materiales (producto_id, material_id)
            VALUES (:producto_id, :material_id)
        ');
        foreach ($materialIds as $materialId) {
            $link->execute([
                ':producto_id' => $productId,
                ':material_id' => $materialId,
            ]);
        }
    }

    private function rollback(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }
}
