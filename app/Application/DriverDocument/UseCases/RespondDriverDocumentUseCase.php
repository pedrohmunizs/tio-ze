<?php

namespace App\Application\DriverDocument\UseCases;

use App\Domain\DriverDocument\Entities\DriverDocument;
use App\Domain\DriverDocument\Repositories\DriverDocumentRepositoryInterface;
use App\Application\DriverDocument\DTOs\RespondDriverDocumentData;

class RespondDriverDocumentUseCase
{
    public function __construct(
        private DriverDocumentRepositoryInterface $repository,
    ) {}

    public function execute(int $id, RespondDriverDocumentData $data): DriverDocument
    {
        $entity = $this->repository->findById($id);

        if (!$entity) {
            throw new \DomainException('DriverDocument não encontrado');
        }

        if (!$entity->isPending()) {
            throw new \DomainException('Esta solicitação já foi respondida');
        }

        $entity->setStatus($data->status);
        $this->repository->save($entity);

        return $entity;
    }
}