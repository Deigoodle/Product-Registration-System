<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\ProductoService;

class ProductoController {
    public function __construct(
        private readonly ProductoService $productoService
    ) {
    }

    public function index(): array
    {
        return $this->productoService->findAll();
    }

    public function show(int $id): array
    {
        return $this->productoService->find($id);
    }

    public function create(array $data): array
    {
        return $this->productoService->create($data);
    }

    public function update(int $id, array $data): array
    {
        return $this->productoService->update($id, $data);
    }

    public function delete(int $id): array
    {
        return $this->productoService->delete($id);
    }
}
