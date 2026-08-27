<?php

namespace App\Application\School\UseCases;

use App\Domain\School\Repositories\SchoolRepositoryInterface;

class DeleteSchoolUseCase
{
    public function __construct(
        private SchoolRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        if (!$this->repository->exists($id)) {
            throw new \RuntimeException('School not found');
        }

        $this->repository->delete($id);
    }
}