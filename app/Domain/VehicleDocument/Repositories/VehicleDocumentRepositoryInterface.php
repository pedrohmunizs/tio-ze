<?php

namespace App\Domain\VehicleDocument\Repositories;

use App\Domain\VehicleDocument\Entities\VehicleDocument;

interface VehicleDocumentRepositoryInterface
{
    public function findById(int $id): ?VehicleDocument;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array;
    public function findByField(string $field, mixed $value): ?VehicleDocument;
    public function findManyByField(string $field, mixed $value): array;
    public function save(VehicleDocument $entity): void;
    public function delete(int $id): void;
    public function exists(int $id): bool;
    public function count(array $filters = []): int;
}