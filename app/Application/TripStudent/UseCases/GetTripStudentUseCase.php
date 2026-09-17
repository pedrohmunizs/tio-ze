<?php

namespace App\Application\TripStudent\UseCases;

use App\Domain\TripStudent\Entities\TripStudent;
use App\Domain\TripStudent\Repositories\TripStudentRepositoryInterface;

class GetTripStudentUseCase
{
    public function __construct(
        private TripStudentRepositoryInterface $repository
    ) {}

    public function execute(int $id): ?TripStudent
    {
        return $this->repository->findById($id);
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findAll($filters, $page, $perPage);
    }
}