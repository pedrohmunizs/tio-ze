<?php

namespace App\Application\Vehicle\UseCases;

use App\Domain\Vehicle\Repositories\VehicleRepositoryInterface;

class DeleteVehicleUseCase
{
    public function __construct(
        private VehicleRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        if (!$this->repository->exists($id)) {
            throw new \RuntimeException('Vehicle not found');
        }

        $this->repository->delete($id);
    }
}