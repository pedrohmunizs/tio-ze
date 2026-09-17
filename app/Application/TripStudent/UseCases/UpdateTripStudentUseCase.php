<?php

namespace App\Application\TripStudent\UseCases;

use App\Domain\TripStudent\Entities\TripStudent;
use App\Domain\TripStudent\Repositories\TripStudentRepositoryInterface;
use App\Application\TripStudent\DTOs\TripStudentData;

class UpdateTripStudentUseCase
{
    public function __construct(
        private TripStudentRepositoryInterface $repository
    ) {}

    public function execute(int $id, TripStudentData $data): TripStudent
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('TripStudent not found');
        }

        $entity->setData($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}