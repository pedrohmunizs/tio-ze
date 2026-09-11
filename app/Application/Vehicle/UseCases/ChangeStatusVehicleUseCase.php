<?php

namespace App\Application\Vehicle\UseCases;

use App\Application\Vehicle\DTOs\ChangeStatusVehicleData;
use App\Domain\Vehicle\Entities\Vehicle;
use App\Domain\Vehicle\Repositories\VehicleRepositoryInterface;

class ChangeStatusVehicleUseCase
{
    public function __construct(private VehicleRepositoryInterface $repository) {}

    public function execute(int $id, ChangeStatusVehicleData $data): Vehicle
    {
        $entity = $this->repository->findById($id);

        if (!$entity) {
            throw new \DomainException('Veículo não encontrado');
        }

        $entity->setStatus($data->status);
        $this->repository->save($entity);

        return $entity;
    }
}