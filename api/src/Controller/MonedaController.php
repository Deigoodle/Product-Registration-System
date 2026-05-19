<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\MonedaService;

class MonedaController {
    public function __construct(
        private readonly MonedaService $monedaService
    ) {
    }

    public function index(): array {
        return $this->monedaService->findAll();
    }

    public function show(int $id): array {
        return $this->monedaService->find($id);
    }

    public function create(array $data): array {
        return $this->monedaService->create($data);
    }

    public function update(int $id, array $data): array {
        return $this->monedaService->update($id, $data);
    }

    public function delete(int $id): array {
        return $this->monedaService->delete($id);
    }
}
