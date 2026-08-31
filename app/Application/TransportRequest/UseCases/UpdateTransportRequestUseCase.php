<?php

namespace App\Application\TransportRequest\UseCases;

use App\Domain\TransportRequest\Entities\TransportRequest;
use App\Domain\TransportRequest\Repositories\TransportRequestRepositoryInterface;
use App\Application\TransportRequest\DTOs\TransportRequestData;

class UpdateTransportRequestUseCase
{
    public function __construct(
        private TransportRequestRepositoryInterface $repository
    ) {}

    public function execute(int $id, TransportRequestData $data): TransportRequest
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('TransportRequest not found');
        }

        $entity->setData($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}