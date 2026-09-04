<?php

namespace App\Application\StopChild\UseCases;

use App\Domain\StopChild\Entities\StopChild;
use App\Domain\StopChild\Repositories\StopChildRepositoryInterface;
use App\Application\StopChild\DTOs\CreateStopChildData;

class CreateStopChildUseCase
{
    public function __construct(
        private StopChildRepositoryInterface $repository
    ) {}

    public function execute(CreateStopChildData $data): StopChild
    {
        $entity = new StopChild($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}