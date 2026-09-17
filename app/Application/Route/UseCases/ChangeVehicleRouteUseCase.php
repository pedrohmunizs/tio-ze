<?php

namespace App\Application\Route\UseCases;

use App\Application\Route\DTOs\ChangeVehicleRouteData;
use App\Domain\Route\Entities\Route;
use App\Domain\Route\Repositories\RouteRepositoryInterface;
use App\Domain\Vehicle\Repositories\VehicleRepositoryInterface;

class ChangeVehicleRouteUseCase
{
    public function __construct(
        private RouteRepositoryInterface $repository,
        private VehicleRepositoryInterface $vehicle_repository,
    ) {}

    public function execute(int $id, ChangeVehicleRouteData $data): Route
    {
        $entity = $this->repository->findById($id);

        if (!$entity) {
            throw new \RuntimeException('Route not found');
        }

        $vehicle = $this->vehicle_repository->findById($data->fk_vehicle);

        if (!$vehicle->isActive()) {
            throw new \DomainException('O veículo não está ativo.');
        }

        $entity->setVehicleId($data->fk_vehicle);
        $this->repository->save($entity);

        return $entity;
    }
}