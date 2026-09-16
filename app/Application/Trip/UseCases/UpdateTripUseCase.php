<?php

namespace App\Application\Trip\UseCases;

use App\Domain\Trip\Entities\Trip;
use App\Domain\Trip\Repositories\TripRepositoryInterface;
use App\Application\Trip\DTOs\TripData;

class UpdateTripUseCase
{
    public function __construct(
        private TripRepositoryInterface $repository
    ) {}

    public function execute(int $id, TripData $data): Trip
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('Trip not found');
        }

        $entity->setData($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}