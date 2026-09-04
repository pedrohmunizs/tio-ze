<?php

namespace App\Application\Stop\UseCases;

use App\Domain\Stop\Entities\Stop;
use App\Domain\Stop\Repositories\StopRepositoryInterface;

class GetStopUseCase
{
    public function __construct(
        private StopRepositoryInterface $repository
    ) {}

    public function execute(int $id): ?Stop
    {
        return $this->repository->findById($id);
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findAll($filters, $page, $perPage);
    }
}