<?php

namespace App\Application\VehicleDocument\UseCases;

use App\Domain\VehicleDocument\Repositories\VehicleDocumentRepositoryInterface;

class DeleteVehicleDocumentUseCase
{
    public function __construct(
        private VehicleDocumentRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        if (!$this->repository->exists($id)) {
            throw new \RuntimeException('VehicleDocument not found');
        }

        $this->repository->delete($id);
    }
}