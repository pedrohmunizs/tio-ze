<?php

namespace App\Application\Stop\UseCases;

use App\Domain\Stop\Entities\Stop;
use App\Domain\Stop\Repositories\StopRepositoryInterface;
use App\Application\Stop\DTOs\StopData;

class UpdateStopUseCase
{
    public function __construct(
        private StopRepositoryInterface $repository
    ) {}

    public function execute(int $id, StopData $data): Stop
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('Stop not found');
        }

        $entity->setData($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}