<?php

namespace App\Application\Driver\UseCases;

use App\Application\Driver\DTOs\CreateDriverData;
use App\Domain\Driver\Entities\Driver;
use App\Domain\Driver\Repositories\DriverRepositoryInterface;

class CreateDriverUseCase
{
    public function __construct(
        private DriverRepositoryInterface $repository
    ) {}

    public function execute(CreateDriverData $data): Driver
    {
        $entity = new Driver(
            userId: $data->fk_user,
            providerId: $data->fk_provider,
            isAutonomous: $data->is_autonomous
        );
        
        $this->repository->save($entity);
        return $entity;
    }
}