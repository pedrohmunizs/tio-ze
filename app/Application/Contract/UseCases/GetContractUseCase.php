<?php

namespace App\Application\Contract\UseCases;

use App\Domain\Contract\Entities\Contract;
use App\Domain\Contract\Repositories\ContractRepositoryInterface;

class GetContractUseCase
{
    public function __construct(
        private ContractRepositoryInterface $repository
    ) {}

    public function execute(int $id): ?Contract
    {
        return $this->repository->findById($id);
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findAll($filters, $page, $perPage);
    }
}