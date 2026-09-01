<?php

namespace App\Application\TransportRequest\UseCases;

use App\Application\Contract\DTOs\CreateContractData;
use App\Application\Contract\UseCases\CreateContractUseCase;
use App\Application\TransportRequest\DTOs\RespondTransportRequestData;
use App\Domain\TransportRequest\Entities\TransportRequest;
use App\Domain\TransportRequest\Repositories\TransportRequestRepositoryInterface;

class RespondTransportRequestUseCase
{
    public function __construct(
        private TransportRequestRepositoryInterface $repository,
        private CreateContractUseCase $create_contract
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

        if ($entity->isAccepted()) {
            $contract_dto = CreateContractData::fromArray([
                "fk_student" => $entity->getStudentId(),
                "fk_route" => $entity->getRouteId(),
                "fk_provider" => $entity->getProviderId(),
                "fk_transport_request" => $entity->getId(),
            ]);

            $this->create_contract->execute($contract_dto);
        }

        return $entity;
    }
}