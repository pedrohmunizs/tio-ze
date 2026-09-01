<?php

namespace App\Application\Contract\UseCases;

use App\Domain\Contract\Entities\Contract;
use App\Domain\Contract\Repositories\ContractRepositoryInterface;
use App\Application\Contract\DTOs\ContractData;

class UpdateContractUseCase
{
    public function __construct(
        private ContractRepositoryInterface $repository
    ) {}

    public function execute(int $id, ContractData $data): Contract
    {
        $entity = $this->repository->findById($id);
        if (!$entity) {
            throw new \RuntimeException('Contract not found');
        }

        $entity->setData($data->toArray());
        $this->repository->save($entity);
        return $entity;
    }
}