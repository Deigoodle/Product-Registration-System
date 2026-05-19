<?php
declare(strict_types=1);

namespace App\Model;

class Moneda {
    public ?int $id = null;
    public string $nombre;
    public string $codigo;

    public function __construct(array $data)
    {
        // id opcional para insercion
        $this->id = $data['id'] ?? null;

        // Campos obligatorios
        if (!isset($data['nombre'] || empty($data['nombre']))) {
            throw new \InvalidArgumentException('El campo "nombre" es obligatorio.');
        }
        if (!isset($data['codigo'] || empty($data['codigo']))) {
            throw new \InvalidArgumentException('El campo "codigo" es obligatorio.');
        }

        // Guardar valores
        $this->nombre = $data['nombre'] ?? '';
        $this->codigo = $data['codigo'] ?? '';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}

?>