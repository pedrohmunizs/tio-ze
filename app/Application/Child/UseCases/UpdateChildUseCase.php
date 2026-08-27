<?php

namespace App\Application\Child\UseCases;

use App\Domain\Child\Entities\Child;
use App\Domain\Child\Repositories\ChildRepositoryInterface;
use App\Application\Child\DTOs\ChildData;

class UpdateChildUseCase
{
    public function __construct(
        private ChildRepositoryInterface $repository
    ) {}

    public function execute(int $id, ChildData $data): Child
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('Child not found');
        }

        $entity->setData($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}