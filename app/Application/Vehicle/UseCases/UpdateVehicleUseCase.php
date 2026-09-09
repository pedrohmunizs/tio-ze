<?php

namespace App\Application\Vehicle\UseCases;

use App\Domain\Vehicle\Entities\Vehicle;
use App\Domain\Vehicle\Repositories\VehicleRepositoryInterface;
use App\Application\Vehicle\DTOs\VehicleData;

class UpdateVehicleUseCase
{
    public function __construct(
        private VehicleRepositoryInterface $repository
    ) {}

    public function execute(int $id, VehicleData $data): Vehicle
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('Vehicle not found');
        }

        $entity->setData($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}