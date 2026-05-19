<?php
declare(strict_types=1);

namespace App\Model;

class Producto {
    public ?int $id = null;
    public string $codigo;
    public string $nombre;
    public string $descripcion;
    public float $precio;
    public int $moneda_id;
    public int $bodega_id;
    public int $sucursal_id;
    /** @var int[] */
    public array $materiales;

    public function __construct(array $data) {
        $this->id = $data['id'] ?? null;

        if (!isset($data['codigo']) || $data['codigo'] === '') {
            throw new \InvalidArgumentException('El campo "codigo" es obligatorio.');
        }
        if (!isset($data['nombre']) || $data['nombre'] === '') {
            throw new \InvalidArgumentException('El campo "nombre" es obligatorio.');
        }
        if (!isset($data['descripcion']) || $data['descripcion'] === '') {
            throw new \InvalidArgumentException('El campo "descripcion" es obligatorio.');
        }
        if (!isset($data['precio']) || !is_numeric($data['precio'])) {
            throw new \InvalidArgumentException('El campo "precio" es obligatorio y debe ser un número.');
        }
        if (!isset($data['moneda_id']) || !is_numeric($data['moneda_id'])) {
            throw new \InvalidArgumentException('El campo "moneda_id" es obligatorio y debe ser un número.');
        }
        if (!isset($data['bodega_id']) || !is_numeric($data['bodega_id'])) {
            throw new \InvalidArgumentException('El campo "bodega_id" es obligatorio y debe ser un número.');
        }
        if (!isset($data['sucursal_id']) || !is_numeric($data['sucursal_id'])) {
            throw new \InvalidArgumentException('El campo "sucursal_id" es obligatorio y debe ser un número.');
        }
        if (!isset($data['materiales']) || !is_array($data['materiales'])) {
            throw new \InvalidArgumentException('El campo "materiales" es obligatorio y debe ser un arreglo de IDs de materiales.');
        }

        $this->codigo = (string) $data['codigo'];
        $this->nombre = (string) $data['nombre'];
        $this->descripcion = (string) $data['descripcion'];
        $this->precio = round((float) $data['precio'], 2);
        $this->moneda_id = (int) $data['moneda_id'];
        $this->bodega_id = (int) $data['bodega_id'];
        $this->sucursal_id = (int) $data['sucursal_id'];
        $this->materiales = array_map('intval', $data['materiales']);
    }

    /** @param int[] $materiales */
    public static function fromRow(array $row, array $materiales): self
    {
        return new self([
            'id' => (int) $row['id'],
            'codigo' => $row['codigo'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'precio' => $row['precio'],
            'moneda_id' => $row['moneda_id'],
            'bodega_id' => $row['bodega_id'],
            'sucursal_id' => $row['sucursal_id'],
            'materiales' => $materiales,
        ]);
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'moneda_id' => $this->moneda_id,
            'bodega_id' => $this->bodega_id,
            'sucursal_id' => $this->sucursal_id,
            'materiales' => $this->materiales,
        ];
    }

    public function toPdoParams(): array {
        return [
            ':codigo' => $this->codigo,
            ':nombre' => $this->nombre,
            ':descripcion' => $this->descripcion,
            ':precio' => $this->precio,
            ':moneda_id' => $this->moneda_id,
            ':bodega_id' => $this->bodega_id,
            ':sucursal_id' => $this->sucursal_id,
        ];
    }
}
