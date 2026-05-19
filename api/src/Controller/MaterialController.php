<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\MaterialService;

class MaterialController {
    public function __construct(
        private readonly MaterialService $materialService
    ) {
    }

    public function index(): array
    {
        return $this->materialService->findAll();
    }

    public function show(int $id): array
    {
        return $this->materialService->find($id);
    }

    public function create(array $data): array
    {
        return $this->materialService->create($data);
    }

    public function update(int $id, array $data): array
    {
        return $this->materialService->update($id, $data);
    }

    public function delete(int $id): array
    {
        return $this->materialService->delete($id);
    }
}
