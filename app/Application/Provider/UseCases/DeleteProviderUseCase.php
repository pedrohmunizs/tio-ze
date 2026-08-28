<?php

namespace App\Application\Provider\UseCases;

use App\Domain\Provider\Repositories\ProviderRepositoryInterface;

class DeleteProviderUseCase
{
    public function __construct(
        private ProviderRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        if (!$this->repository->exists($id)) {
            throw new \RuntimeException('Provider not found');
        }

        $this->repository->delete($id);
    }
}