<?php

namespace App\Application\TransportRequest\UseCases;

use App\Domain\TransportRequest\Repositories\TransportRequestRepositoryInterface;

class DeleteTransportRequestUseCase
{
    public function __construct(
        private TransportRequestRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        if (!$this->repository->exists($id)) {
            throw new \RuntimeException('TransportRequest not found');
        }

        $this->repository->delete($id);
    }
}