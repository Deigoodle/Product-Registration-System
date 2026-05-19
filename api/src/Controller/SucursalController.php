<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\SucursalService;

class SucursalController {
    public function __construct(
        private readonly SucursalService $sucursalService
    ) {
    }

    public function index(): array {
        return $this->sucursalService->findAll();
    }

    public function show(int $id): array {
        return $this->sucursalService->find($id);
    }

    public function showByBodega(int $id): array {
        return $this->sucursalService->findByBodega($id);
    }

    public function create(array $data): array {
        return $this->sucursalService->create($data);
    }

    public function update(int $id, array $data): array {
        return $this->sucursalService->update($id, $data);
    }

    public function delete(int $id): array {
        return $this->sucursalService->delete($id);
    }
}
