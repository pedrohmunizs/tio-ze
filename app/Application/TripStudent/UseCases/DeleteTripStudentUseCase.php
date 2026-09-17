<?php

namespace App\Application\TripStudent\UseCases;

use App\Domain\TripStudent\Repositories\TripStudentRepositoryInterface;

class DeleteTripStudentUseCase
{
    public function __construct(
        private TripStudentRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        if (!$this->repository->exists($id)) {
            throw new \RuntimeException('TripStudent not found');
        }

        $this->repository->delete($id);
    }
}