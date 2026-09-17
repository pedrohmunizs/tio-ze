<?php

namespace App\Application\Trip\UseCases;

use App\Application\Trip\DTOs\ChangeVehicleTripData;
use App\Domain\Trip\Entities\Trip;
use App\Domain\Trip\Repositories\TripRepositoryInterface;

class ChangeVehicleTripUseCase
{
    public function __construct(
        private TripRepositoryInterface $repository,
    ) {}

    public function execute(int $id, ChangeVehicleTripData $data): Trip
    {
        $entity = $this->repository->findById($id);

        if (!$entity) {
            throw new \RuntimeException('Trip not found');
        }

        $entity->setVehicleId($data->fk_vehicle);
        $this->repository->save($entity);

        return $entity;
    }
}