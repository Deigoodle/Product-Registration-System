<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\BodegaService;

class BodegaController {
    public function __construct(
        private readonly BodegaService $bodegaService
    ) {
    }

    public function index(): array
    {
        return $this->bodegaService->findAll();
    }

    public function show(int $id): array
    {
        return $this->bodegaService->find($id);
    }

    public function create(array $data): array
    {
        return $this->bodegaService->create($data);
    }

    public function update(int $id, array $data): array
    {
        return $this->bodegaService->update($id, $data);
    }

    public function delete(int $id): array
    {
        return $this->bodegaService->delete($id);
    }
}
