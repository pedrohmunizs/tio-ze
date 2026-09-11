<?php

namespace App\Application\Driver\UseCases;

use App\Application\Driver\DTOs\ChangeStatusDriverData;
use App\Domain\Driver\Entities\Driver;
use App\Domain\Driver\Repositories\DriverRepositoryInterface;

class ChangeStatusDriverUseCase
{
    public function __construct(private DriverRepositoryInterface $repository) {}

    public function execute(int $id, ChangeStatusDriverData $data): Driver
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('Driver not found');
        }

        $entity->setStatus($data->status);
        $this->repository->save($entity);
        
        return $entity;
    }
}