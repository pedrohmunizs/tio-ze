<?php

namespace App\Application\DriverDocument\UseCases;

use App\Domain\DriverDocument\Entities\DriverDocument;
use App\Domain\DriverDocument\Repositories\DriverDocumentRepositoryInterface;
use App\Application\DriverDocument\DTOs\DriverDocumentData;

class UpdateDriverDocumentUseCase
{
    public function __construct(
        private DriverDocumentRepositoryInterface $repository
    ) {}

    public function execute(int $id, DriverDocumentData $data): DriverDocument
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('DriverDocument not found');
        }

        $entity->setData($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}