<?php

namespace App\Application\Route\UseCases;

use App\Domain\Route\Repositories\RouteRepositoryInterface;

class DeleteRouteUseCase
{
    public function __construct(
        private RouteRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        if (!$this->repository->exists($id)) {
            throw new \RuntimeException('Route not found');
        }

        $this->repository->delete($id);
    }
}