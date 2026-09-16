<?php

namespace App\Application\Trip\UseCases;

use App\Domain\Trip\Repositories\TripRepositoryInterface;

class DeleteTripUseCase
{
    public function __construct(
        private TripRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        if (!$this->repository->exists($id)) {
            throw new \RuntimeException('Trip not found');
        }

        $this->repository->delete($id);
    }
}