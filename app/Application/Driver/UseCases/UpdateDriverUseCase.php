<?php

namespace App\Application\Driver\UseCases;

use App\Domain\Driver\Entities\Driver;
use App\Domain\Driver\Repositories\DriverRepositoryInterface;
use App\Application\Driver\DTOs\UpdateDriverData;

class UpdateDriverUseCase
{
    public function __construct(
        private DriverRepositoryInterface $repository
    ) {}

    public function execute(int $id, UpdateDriverData $data): Driver
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('Driver not found');
        }

        $entity->update(
            license_number: $data->license_number,
            license_category: $data->license_category,
            license_valid_until: $data->license_valid_until->format('Y-m-d'),
        );

        $this->repository->save($entity);
        return $entity;
    }
}