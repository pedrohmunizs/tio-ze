<?php

namespace App\Application\Child\UseCases;

use App\Domain\Child\Repositories\ChildRepositoryInterface;

class DeleteChildUseCase
{
    public function __construct(
        private ChildRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        if (!$this->repository->exists($id)) {
            throw new \RuntimeException('Child not found');
        }

        $this->repository->delete($id);
    }
}