<?php

namespace App\Application\Route\UseCases;

use App\Domain\Route\Entities\Route;
use App\Domain\Route\Repositories\RouteRepositoryInterface;

class GetRouteUseCase
{
    public function __construct(
        private RouteRepositoryInterface $repository
    ) {}

    public function execute(int $id): ?Route
    {
        return $this->repository->findById($id);
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findAll($filters, $page, $perPage);
    }
}