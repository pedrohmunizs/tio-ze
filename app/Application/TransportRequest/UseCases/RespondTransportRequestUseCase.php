<?php

namespace App\Application\TransportRequest\UseCases;

use App\Application\Contract\DTOs\CreateContractData;
use App\Application\Contract\UseCases\CreateContractUseCase;
use App\Application\Route\UseCases\OptimizePickupRouteUseCase;
use App\Application\Stop\UseCases\CreateStopUseCase;
use App\Application\StopChild\UseCases\CreateStopChildUseCase;
use App\Application\TransportRequest\DTOs\RespondTransportRequestData;
use App\Domain\Stop\Repositories\StopRepositoryInterface;
use App\Domain\StopChild\Repositories\StopChildRepositoryInterface;
use App\Domain\TransportRequest\Entities\TransportRequest;
use App\Domain\TransportRequest\Repositories\TransportRequestRepositoryInterface;

class RespondTransportRequestUseCase
{
    public function __construct(
        private TransportRequestRepositoryInterface $repository,
        private StopRepositoryInterface $stop_repository,
        private StopChildRepositoryInterface $stop_child_repository,
        private CreateContractUseCase $create_contract,
        private CreateStopUseCase $create_stop_use_case,
        private OptimizePickupRouteUseCase $optimize_route_use_case,
        private CreateStopChildUseCase $create_stop_child_use_case,
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

        $entity->respond($data->status, $data->message);

        $this->repository->save($entity);

        if ($entity->isAccepted()) {
            $contract_dto = CreateContractData::fromArray([
                "fk_student" => $entity->getStudentId(),
                "fk_route" => $entity->getRouteId(),
                "fk_provider" => $entity->getProviderId(),
                "fk_transport_request" => $entity->getId(),
            ]);

            $this->create_contract->execute($contract_dto);

            $student = $entity->getStudent();
            $route = $entity->getRoute();

            $types = ['going', 'returning'];

            foreach ($types as $type) {

                $fk_stop = $this->stop_repository->existsStopByAddress($entity->getRouteId(), $entity->getStudent()->getAddress()->getZipCode(), $entity->getStudent()->getAddress()->getNumber(), $type);

                if (!$fk_stop) {
                    $this->create_stop_use_case->execute(
                        fk_route: $route->getId(),
                        fk_student: $student->getId(),
                        fk_address: $student->getAddressId(),
                        type: $type,
                    );

                    $this->optimize_route_use_case->execute($entity->getRouteId(), $type);
                } else {
                    $stop_order = $this->stop_child_repository->getMaxOrder($fk_stop);

                    $this->create_stop_child_use_case->execute(
                        fk_stop: $fk_stop,
                        fk_child: $student->getId(),
                        stop_order: ($stop_order + 1)
                    );
                }
            }
        }

        return $entity;
    }
}