<?php

namespace App\Application\Trip\UseCases;

use App\Domain\Trip\Entities\Trip;
use App\Domain\Trip\Repositories\TripRepositoryInterface;
use App\Application\Trip\DTOs\CreateTripData;

class CreateTripUseCase
{
    public function __construct(
        private TripRepositoryInterface $repository
    ) {}

    public function execute(CreateTripData $data): Trip
    {
        $entity = new Trip(
            fk_route: $data->fk_route,
            fk_vehicle: $data->fk_vehicle,
            fk_driver: $data->fk_driver,
            date: $data->date,
            type: $data->type
        );
        
        $this->repository->save($entity);
        return $entity;
    }
}