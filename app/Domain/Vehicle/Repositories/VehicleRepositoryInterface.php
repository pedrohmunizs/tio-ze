<?php

namespace App\Domain\Vehicle\Repositories;

use App\Domain\Vehicle\Entities\Vehicle;

interface VehicleRepositoryInterface
{
    public function findById(int $id): ?Vehicle;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array;
    public function findByField(string $field, mixed $value): ?Vehicle;
    public function findManyByField(string $field, mixed $value): array;
    public function save(Vehicle $entity): void;
    public function delete(int $id): void;
    public function exists(int $id): bool;
    public function count(array $filters = []): int;
}