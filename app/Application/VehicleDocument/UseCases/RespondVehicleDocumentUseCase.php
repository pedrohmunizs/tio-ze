<?php

namespace App\Application\VehicleDocument\UseCases;

use App\Application\VehicleDocument\DTOs\RespondVehicleDocumentData;
use App\Domain\VehicleDocument\Entities\VehicleDocument;
use App\Domain\VehicleDocument\Repositories\VehicleDocumentRepositoryInterface;

class RespondVehicleDocumentUseCase
{
    public function __construct(
        private VehicleDocumentRepositoryInterface $repository
    ) {}

    public function execute(int $id, RespondVehicleDocumentData $data): VehicleDocument
    {
        $entity = $this->repository->findById($id);

        if (!$entity) {
            throw new \RuntimeException('VehicleDocument not found');
        }

        if (!$entity->isPending()) {
            throw new \DomainException('Esta solicitação já foi respondida');
        }

        $entity->setStatus($data->status);

        $this->repository->save($entity);

        return $entity;
    }
}