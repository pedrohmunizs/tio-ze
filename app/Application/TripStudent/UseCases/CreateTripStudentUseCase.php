<?php

namespace App\Application\TripStudent\UseCases;

use App\Domain\TripStudent\Entities\TripStudent;
use App\Domain\TripStudent\Repositories\TripStudentRepositoryInterface;
use App\Application\TripStudent\DTOs\CreateTripStudentData;

class CreateTripStudentUseCase
{
    public function __construct(
        private TripStudentRepositoryInterface $repository
    ) {}

    public function execute(CreateTripStudentData $data): TripStudent
    {
        $entity = new TripStudent(
            fk_trip: $data->fk_trip,
            fk_student: $data->fk_student,
        );
        
        $this->repository->save($entity);
        return $entity;
    }
}