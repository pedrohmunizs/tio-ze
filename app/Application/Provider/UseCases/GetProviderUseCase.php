<?php

namespace App\Application\Provider\UseCases;

use App\Domain\Provider\Entities\Provider;
use App\Domain\Provider\Repositories\ProviderRepositoryInterface;

class GetProviderUseCase
{
    public function __construct(
        private ProviderRepositoryInterface $repository
    ) {}

    public function execute(int $id): ?Provider
    {
        return $this->repository->findById($id);
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findAll($filters, $page, $perPage);
    }
}