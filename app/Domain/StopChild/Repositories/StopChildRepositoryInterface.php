<?php

namespace App\Domain\StopChild\Repositories;

use App\Domain\StopChild\Entities\StopChild;

interface StopChildRepositoryInterface
{
    public function findById(int $id): ?StopChild;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array;
    public function findByField(string $field, mixed $value): ?StopChild;
    public function findManyByField(string $field, mixed $value): array;
    public function save(StopChild $entity): void;
    public function delete(int $id): void;
    public function exists(int $id): bool;
    public function count(array $filters = []): int;
    public function deleteByStopIdAndChildId(int $fk_stop, int $fk_child): void;
    public function getMaxOrder(int $fk_stop): int;
}