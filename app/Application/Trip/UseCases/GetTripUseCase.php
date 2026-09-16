<?php

namespace App\Application\Trip\UseCases;

use App\Domain\Trip\Entities\Trip;
use App\Domain\Trip\Repositories\TripRepositoryInterface;

class GetTripUseCase
{
    public function __construct(
        private TripRepositoryInterface $repository
    ) {}

    public function execute(int $id): ?Trip
    {
        return $this->repository->findById($id);
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findAll($filters, $page, $perPage);
    }
}