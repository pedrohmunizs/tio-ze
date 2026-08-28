<?php

namespace App\Domain\Provider\Repositories;

use App\Domain\Provider\Entities\Provider;

interface ProviderRepositoryInterface
{
    public function findById(int $id): ?Provider;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array;
    public function findByField(string $field, mixed $value): ?Provider;
    public function findManyByField(string $field, mixed $value): array;
    public function save(Provider $entity): void;
    public function delete(int $id): void;
    public function exists(int $id): bool;
    public function count(array $filters = []): int;
}