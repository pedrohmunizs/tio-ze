<?php

namespace App\Application\StopChild\UseCases;

use App\Domain\StopChild\Entities\StopChild;
use App\Domain\StopChild\Repositories\StopChildRepositoryInterface;
use App\Application\StopChild\DTOs\StopChildData;

class UpdateStopChildUseCase
{
    public function __construct(
        private StopChildRepositoryInterface $repository
    ) {}

    public function execute(int $id, StopChildData $data): StopChild
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('StopChild not found');
        }

        $entity->setData($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}