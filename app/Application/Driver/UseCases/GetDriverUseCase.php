<?php

namespace App\Application\Driver\UseCases;

use App\Domain\Driver\Entities\Driver;
use App\Domain\Driver\Repositories\DriverRepositoryInterface;

class GetDriverUseCase
{
    public function __construct(
        private DriverRepositoryInterface $repository
    ) {}

    public function execute(int $id): ?Driver
    {
        return $this->repository->findById($id);
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findAll($filters, $page, $perPage);
    }
}