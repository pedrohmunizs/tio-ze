<?php

namespace App\Domain\Trip\Repositories;

use App\Domain\Trip\Entities\Trip;

interface TripRepositoryInterface
{
    public function findById(int $id): ?Trip;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array;
    public function findByField(string $field, mixed $value): ?Trip;
    public function findManyByField(string $field, mixed $value): array;
    public function save(Trip $entity): void;
    public function delete(int $id): void;
    public function exists(int $id): bool;
    public function count(array $filters = []): int;
    public function existsByRouteAndDate(int $fk_route, string $date): bool;
}