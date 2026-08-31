<?php

namespace App\Application\Route\UseCases;

use App\Domain\Route\Entities\Route;
use App\Domain\Route\Repositories\RouteRepositoryInterface;
use App\Application\Route\DTOs\RouteData;

class UpdateRouteUseCase
{
    public function __construct(
        private RouteRepositoryInterface $repository
    ) {}

    public function execute(int $id, RouteData $data): Route
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('Route not found');
        }

        $entity->setData($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}