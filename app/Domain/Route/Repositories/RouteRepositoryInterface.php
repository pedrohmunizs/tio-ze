<?php

namespace App\Domain\Route\Repositories;

use App\Domain\Route\Entities\Route;

interface RouteRepositoryInterface
{
    public function findById(int $id): ?Route;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array;
    public function findByField(string $field, mixed $value): ?Route;
    public function findManyByField(string $field, mixed $value): array;
    public function save(Route $entity): void;
    public function delete(int $id): void;
    public function exists(int $id): bool;
    public function existsByProviderAndName(int $provider_id, string $name) : bool;
    public function count(array $filters = []): int;
}