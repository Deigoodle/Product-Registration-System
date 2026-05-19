<?php
declare(strict_types=1);

namespace App\Validator;

use App\Model\Producto;
use App\Model\Material;

class ProductoValidator {
    public static function validate(Producto $producto, array $existingMaterialIds): array {
        $errors = [];

        // Codigo
        if (strlen($producto->codigo) < 5 || strlen($producto->codigo) > 15) {
            $errors[] = "El campo 'codigo' debe tener entre 5 y 15 caracteres.";
        }

        // Nombre
        if (strlen($producto->nombre) < 2 || strlen($producto->nombre) > 50) {
            $errors[] = "El campo 'nombre' debe tener entre 2 y 50 caracteres.";
        }

        // Precio
        if ($producto->precio <= 0) {
            $errors[] = "El campo 'precio' debe ser un número positivo.";
        }

        // Descripcion
        if (strlen($producto->descripcion) < 10 || strlen($producto->descripcion) > 1000) {
            $errors[] = "El campo 'descripcion' debe tener entre 10 y 1000 caracteres.";
        }

        // Minimo de materiales
        $minimo_materiales = 2;
        if (count($producto->materiales) < $minimo_materiales) {
            $errors[] = "El producto debe tener al menos {$minimo_materiales} materiales.";
        }

        // Validar que los materiales existan
        foreach ($producto->materiales as $materialId) {
            if (!in_array($materialId, $existingMaterialIds)) {
                $errors[] = "El material con ID {$materialId} no existe.";
            }
        }

        return $errors;
    }
}

?>