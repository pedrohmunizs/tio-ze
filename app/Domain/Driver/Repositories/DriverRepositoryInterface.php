<?php

namespace App\Domain\Driver\Repositories;

use App\Domain\Driver\Entities\Driver;

interface DriverRepositoryInterface
{
    public function findById(int $id): ?Driver;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array;
    public function findByField(string $field, mixed $value): ?Driver;
    public function findManyByField(string $field, mixed $value): array;
    public function save(Driver $entity): void;
    public function delete(int $id): void;
    public function exists(int $id): bool;
    public function count(array $filters = []): int;
}