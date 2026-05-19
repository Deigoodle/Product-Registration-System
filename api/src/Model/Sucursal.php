<?php
declare(strict_types=1);

namespace App\Model;

class Sucursal {
    public ?int $id = null;
    public string $nombre;
    public int $bodega_id;

    public function __construct(array $data)
    {
        // id opcional para insercion
        $this->id = $data['id'] ?? null;

        // Campos obligatorios
        if (!isset($data['nombre']) || $data['nombre'] === '') {
            throw new \InvalidArgumentException('El campo "nombre" es obligatorio.');
        }
        if (!isset($data['bodega_id']) || !is_numeric($data['bodega_id'])) {
            throw new \InvalidArgumentException('El campo "bodega_id" es obligatorio y debe ser un número.');
        }

        // Guardar valores
        $this->nombre = $data['nombre'];
        $this->bodega_id = $data['bodega_id'];  
    }

    public static function fromRow(array $row): self
    {
        return new self([
            'id' => (int) $row['id'],
            'nombre' => $row['nombre'],
            'bodega_id' => (int) $row['bodega_id'],
        ]);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'bodega_id' => $this->bodega_id,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}

?>