<?php

namespace App\Application\StopChild\UseCases;

use App\Domain\StopChild\Entities\StopChild;
use App\Domain\StopChild\Repositories\StopChildRepositoryInterface;

class GetStopChildUseCase
{
    public function __construct(
        private StopChildRepositoryInterface $repository
    ) {}

    public function execute(int $id): ?StopChild
    {
        return $this->repository->findById($id);
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findAll($filters, $page, $perPage);
    }
}