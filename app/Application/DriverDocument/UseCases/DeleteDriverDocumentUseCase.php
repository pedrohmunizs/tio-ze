<?php

namespace App\Application\DriverDocument\UseCases;

use App\Domain\DriverDocument\Repositories\DriverDocumentRepositoryInterface;

class DeleteDriverDocumentUseCase
{
    public function __construct(
        private DriverDocumentRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        if (!$this->repository->exists($id)) {
            throw new \RuntimeException('DriverDocument not found');
        }

        $this->repository->delete($id);
    }
}