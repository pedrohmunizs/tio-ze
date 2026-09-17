<?php

namespace App\Application\Route\UseCases;

use App\Application\Route\DTOs\ChangeDriverRouteData;
use App\Application\Trip\DTOs\ChangeDriverTripData;
use App\Application\Trip\UseCases\ChangeDriverTripUseCase;
use App\Domain\Driver\Repositories\DriverRepositoryInterface;
use App\Domain\Route\Entities\Route;
use App\Domain\Route\Repositories\RouteRepositoryInterface;
use App\Domain\Trip\Repositories\TripRepositoryInterface;

class ChangeDriverRouteUseCase
{
    public function __construct(
        private RouteRepositoryInterface $repository,
        private DriverRepositoryInterface $driver_repository,
        private TripRepositoryInterface $trip_repository,
        private OptimizePickupRouteUseCase $optimize_route_use_case,
        private ChangeDriverTripUseCase $change_driver_trip_use_case,
    ) {}

    public function execute(int $id, ChangeDriverRouteData $data): Route
    {
        $entity = $this->repository->findById($id);

        if (!$entity) {
            throw new \RuntimeException('Route not found');
        }

        $driver = $this->driver_repository->findById($data->fk_driver);

        if (!$driver->isActive()) {
            throw new \DomainException('O motorista não está ativo.');
        }

        $entity->setDriverId($data->fk_driver);
        $this->repository->save($entity);

        $types = ['going', 'returning'];

        foreach ($types as $type) {
            $this->optimize_route_use_case->execute($id, $type);
        }

        $trips = $this->trip_repository->findByRouteIdWhereStatusScheduled($id);

        foreach ($trips as $trip) {
            $trip_dto = new ChangeDriverTripData($data->fk_driver);
            $this->change_driver_trip_use_case->execute($trip->getId(), $trip_dto);
        }

        return $entity;
    }
}