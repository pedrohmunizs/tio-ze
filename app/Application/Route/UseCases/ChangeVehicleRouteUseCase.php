<?php

namespace App\Application\Route\UseCases;

use App\Application\Route\DTOs\ChangeVehicleRouteData;
use App\Application\Trip\DTOs\ChangeVehicleTripData;
use App\Application\Trip\UseCases\ChangeVehicleTripUseCase;
use App\Domain\Route\Entities\Route;
use App\Domain\Route\Repositories\RouteRepositoryInterface;
use App\Domain\Trip\Repositories\TripRepositoryInterface;
use App\Domain\Vehicle\Repositories\VehicleRepositoryInterface;

class ChangeVehicleRouteUseCase
{
    public function __construct(
        private RouteRepositoryInterface $repository,
        private VehicleRepositoryInterface $vehicle_repository,
        private TripRepositoryInterface $trip_repository,
        private ChangeVehicleTripUseCase $change_vehicle_trip_use_case,
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

        $trips = $this->trip_repository->findByRouteIdWhereStatusScheduled($id);

        foreach ($trips as $trip) {
            $dto = new ChangeVehicleTripData($data->fk_vehicle);
            $this->change_vehicle_trip_use_case->execute($trip->getId(), $dto);
        }

        return $entity;
    }
}