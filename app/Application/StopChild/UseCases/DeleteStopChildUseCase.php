<?php

namespace App\Application\StopChild\UseCases;

use App\Domain\StopChild\Repositories\StopChildRepositoryInterface;

class DeleteStopChildUseCase
{
    public function __construct(
        private StopChildRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        if (!$this->repository->exists($id)) {
            throw new \RuntimeException('StopChild not found');
        }

        $this->repository->delete($id);
    }
}