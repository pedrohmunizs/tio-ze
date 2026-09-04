<?php

namespace App\Application\Stop\UseCases;

use App\Domain\Stop\Repositories\StopRepositoryInterface;

class DeleteStopUseCase
{
    public function __construct(
        private StopRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        if (!$this->repository->exists($id)) {
            throw new \RuntimeException('Stop not found');
        }

        $this->repository->delete($id);
    }
}