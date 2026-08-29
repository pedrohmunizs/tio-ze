<?php

namespace App\Application\Driver\UseCases;

use App\Domain\Driver\Entities\Driver;
use App\Domain\Driver\Repositories\DriverRepositoryInterface;
use App\Application\Driver\DTOs\DriverData;

class UpdateDriverUseCase
{
    public function __construct(
        private DriverRepositoryInterface $repository
    ) {}

    public function execute(int $id, DriverData $data): Driver
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('Driver not found');
        }

        $entity->setData($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}