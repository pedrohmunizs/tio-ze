<?php

namespace App\Application\TransportRequest\UseCases;

use App\Domain\TransportRequest\Entities\TransportRequest;
use App\Domain\TransportRequest\Repositories\TransportRequestRepositoryInterface;

class GetTransportRequestUseCase
{
    public function __construct(
        private TransportRequestRepositoryInterface $repository
    ) {}

    public function execute(int $id): ?TransportRequest
    {
        return $this->repository->findById($id);
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findAll($filters, $page, $perPage);
    }
}