<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\ProductoService;

class ProductosController {
    private ProductoService $productoService;

    public function __construct() {
        $this->productoService = new ProductoService();
    }

    /**
     * Endpoint: POST /productos
     * Creates a new product
     */
    public function create(array $data): array {
        return $this->productoService->create($data);
    }

    /**
     * Get all products (for testing)
     */
    public function index(): array {
        // This would typically query the database for existing products
        return ['productos' => []];
    }

    /**
     * Test endpoint to verify database connection
     */
    public function testConnection(): array {
        $connection = new \PDO(
            getenv('DB_DSN') ?? 'sqlite::memory:',
            getenv('DB_USER') ?? '',
            getenv('DB_PASS') ?? ''
        );
        
        return [
            'success' => true,
            'message' => 'Database connection successful',
        ];
    }
}
