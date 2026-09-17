<?php

namespace App\Domain\Child\Repositories;

use App\Domain\Child\Entities\Child;

interface ChildRepositoryInterface
{
    public function findById(int $id): ?Child;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array;
    public function findByField(string $field, mixed $value): ?Child;
    public function findManyByField(string $field, mixed $value): array;
    public function findByRouteId(int $route_id): array;
    public function save(Child $entity): void;
    public function delete(int $id): void;
    public function exists(int $id): bool;
    public function count(array $filters = []): int;
}