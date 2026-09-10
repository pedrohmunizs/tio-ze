<?php

namespace App\Application\VehicleDocument\UseCases;

use App\Domain\VehicleDocument\Entities\VehicleDocument;
use App\Domain\VehicleDocument\Repositories\VehicleDocumentRepositoryInterface;
use App\Application\VehicleDocument\DTOs\VehicleDocumentData;

class UpdateVehicleDocumentUseCase
{
    public function __construct(
        private VehicleDocumentRepositoryInterface $repository
    ) {}

    public function execute(int $id, VehicleDocumentData $data): VehicleDocument
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('VehicleDocument not found');
        }

        $entity->setData($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}