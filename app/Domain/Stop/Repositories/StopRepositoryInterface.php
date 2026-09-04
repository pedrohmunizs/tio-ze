<?php

namespace App\Domain\Stop\Repositories;

use App\Domain\Stop\Entities\Stop;

interface StopRepositoryInterface
{
    public function findById(int $id): ?Stop;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array;
    public function findByField(string $field, mixed $value): ?Stop;
    public function findManyByField(string $field, mixed $value): array;
    public function save(Stop $entity): void;
    public function delete(int $id): void;
    public function exists(int $id): bool;
    public function count(array $filters = []): int;
}