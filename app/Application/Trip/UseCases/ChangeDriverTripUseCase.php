<?php

namespace App\Application\Trip\UseCases;

use App\Application\Trip\DTOs\ChangeDriverTripData;
use App\Domain\Trip\Entities\Trip;
use App\Domain\Trip\Repositories\TripRepositoryInterface;

class ChangeDriverTripUseCase
{
    public function __construct(
        private TripRepositoryInterface $repository,
    ) {}

    public function execute(int $id, ChangeDriverTripData $data): Trip
    {
        $entity = $this->repository->findById($id);

        if (!$entity) {
            throw new \RuntimeException('Trip not found');
        }

        $entity->setDriverId($data->fk_driver);
        $this->repository->save($entity);

        return $entity;
    }
}