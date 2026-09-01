<?php

namespace App\Application\Contract\UseCases;

use App\Domain\Contract\Repositories\ContractRepositoryInterface;

class DeleteContractUseCase
{
    public function __construct(
        private ContractRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        if (!$this->repository->exists($id)) {
            throw new \RuntimeException('Contract not found');
        }

        $this->repository->delete($id);
    }
}