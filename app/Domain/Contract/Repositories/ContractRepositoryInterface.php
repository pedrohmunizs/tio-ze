<?php

namespace App\Domain\Contract\Repositories;

use App\Domain\Contract\Entities\Contract;

interface ContractRepositoryInterface
{
    public function findById(int $id): ?Contract;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array;
    public function findByField(string $field, mixed $value): ?Contract;
    public function findManyByField(string $field, mixed $value): array;
    public function save(Contract $entity): void;
    public function delete(int $id): void;
    public function exists(int $id): bool;
    public function count(array $filters = []): int;
}