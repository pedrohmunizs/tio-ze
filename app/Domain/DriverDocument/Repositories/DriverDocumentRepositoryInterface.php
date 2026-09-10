<?php

namespace App\Domain\DriverDocument\Repositories;

use App\Domain\DriverDocument\Entities\DriverDocument;

interface DriverDocumentRepositoryInterface
{
    public function findById(int $id): ?DriverDocument;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array;
    public function findByField(string $field, mixed $value): ?DriverDocument;
    public function findManyByField(string $field, mixed $value): array;
    public function save(DriverDocument $entity): void;
    public function delete(int $id): void;
    public function exists(int $id): bool;
    public function count(array $filters = []): int;
}