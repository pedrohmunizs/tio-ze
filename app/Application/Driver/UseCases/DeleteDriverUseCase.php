<?php

namespace App\Application\Driver\UseCases;

use App\Domain\Driver\Repositories\DriverRepositoryInterface;

class DeleteDriverUseCase
{
    public function __construct(
        private DriverRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        if (!$this->repository->exists($id)) {
            throw new \RuntimeException('Driver not found');
        }

        $this->repository->delete($id);
    }
}