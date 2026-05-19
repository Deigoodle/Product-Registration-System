<?php
declare(strict_types=1);

namespace App\Model;

class Bodega {
    public ?int $id = null;
    public string $nombre;

    public function __construct(array $data)
    {
        // id opcional para insercion
        $this->id = $data['id'] ?? null;

        // Campos obligatorios
        if (!isset($data['nombre']) || $data['nombre'] === '') {
            throw new \InvalidArgumentException('El campo "nombre" es obligatorio.');
        }

        $this->nombre = $data['nombre'];
    }

    public static function fromRow(array $row): self
    {
        return new self([
            'id' => (int) $row['id'],
            'nombre' => $row['nombre'],
        ]);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}

?>