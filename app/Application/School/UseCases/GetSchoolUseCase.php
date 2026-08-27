<?php

namespace App\Application\School\UseCases;

use App\Domain\School\Entities\School;
use App\Domain\School\Repositories\SchoolRepositoryInterface;

class GetSchoolUseCase
{
    public function __construct(
        private SchoolRepositoryInterface $repository
    ) {}

    public function execute(int $id): ?School
    {
        return $this->repository->findById($id);
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findAll($filters, $page, $perPage);
    }
}