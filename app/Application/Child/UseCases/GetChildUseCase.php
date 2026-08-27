<?php

namespace App\Application\Child\UseCases;

use App\Domain\Child\Entities\Child;
use App\Domain\Child\Repositories\ChildRepositoryInterface;

class GetChildUseCase
{
    public function __construct(
        private ChildRepositoryInterface $repository
    ) {}

    public function execute(int $id): ?Child
    {
        return $this->repository->findById($id);
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findAll($filters, $page, $perPage);
    }
}