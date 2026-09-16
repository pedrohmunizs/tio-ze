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
        $entity = new Trip($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}