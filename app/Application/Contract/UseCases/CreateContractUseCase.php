<?php

namespace App\Application\Contract\UseCases;

use App\Domain\Contract\Entities\Contract;
use App\Domain\Contract\Repositories\ContractRepositoryInterface;
use App\Application\Contract\DTOs\CreateContractData;
use App\Domain\Contract\Enums\ContractStatus;

class CreateContractUseCase
{
    public function __construct(
        private ContractRepositoryInterface $repository
    ) {}

    public function execute(CreateContractData $data): Contract
    {
        $entity = new Contract(
            fk_student: $data->fk_student,
            fk_route: $data->fk_route,
            fk_provider: $data->fk_provider,
            fk_transport_request: $data->fk_transport_request,
            status:ContractStatus::ACTIVE
        );

        $this->repository->save($entity);
        return $entity;
    }
}