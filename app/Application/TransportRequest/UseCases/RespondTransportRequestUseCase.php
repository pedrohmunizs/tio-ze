<?php

namespace App\Application\TransportRequest\UseCases;

use App\Application\TransportRequest\DTOs\RespondTransportRequestData;
use App\Domain\TransportRequest\Entities\TransportRequest;
use App\Domain\TransportRequest\Repositories\TransportRequestRepositoryInterface;

class RespondTransportRequestUseCase
{
    public function __construct(
        private TransportRequestRepositoryInterface $repository
    ) {}

    public function execute(int $id, RespondTransportRequestData $data): TransportRequest
    {
        $entity = $this->repository->findById($id);

        if (!$entity) {
            throw new \RuntimeException('TransportRequest not found');
        }

        if ($entity->getProviderId() !== user()->provider->id) {
            throw new \DomainException('Você não tem permissão para responder esta solicitação');
        }

        if (!$entity->isPending()) {
            throw new \DomainException('Esta solicitação já foi respondida');
        }

        $entity->respond(
            $data->status,
            $data->message
        );

        $this->repository->save($entity);
        return $entity;
    }
}