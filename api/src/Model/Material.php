<?php
declare(strict_types=1);

namespace App\Model;

class Material {
    public ?int $id = null;
    public string $nombre;
    /** @var int[] */
    public array $productos;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->nombre = $data['nombre'] ?? '';
        $this->productos = is_array($data['productos']) 
            ? array_map('intval', $data['productos'])
            : [];
    }

    public static function fromRow(array $row): self
    {
        return new self([
            'id' => (int) $row['id'],
            'nombre' => $row['nombre'],
            'productos' => [],
        ]);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'productos' => $this->productos,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}

?>