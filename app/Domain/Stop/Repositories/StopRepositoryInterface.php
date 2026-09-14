<?php

namespace App\Domain\Stop\Repositories;

use App\Domain\Stop\Entities\Stop;
use Illuminate\Support\Collection;

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
    public function findByRouteId(int $fk_route, string $type) : array;
    public function updateOrder(int $id, int $stop_order): void;
    public function getMaxOrder(int $fk_route, string $type): int;
    public function findByChildId(int $fk_child): array;
    public function existsStopByAddress(int $fk_route, string $zip_code, string $number, string $type): ?int;
}