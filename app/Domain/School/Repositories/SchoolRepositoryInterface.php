<?php

namespace App\Domain\School\Repositories;

use App\Domain\School\Entities\School;

interface SchoolRepositoryInterface
{
    public function findById(int $id): ?School;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array;
    public function findByField(string $field, mixed $value): ?School;
    public function findManyByField(string $field, mixed $value): array;
    public function save(School $entity): void;
    public function delete(int $id): void;
    public function exists(int $id): bool;
    public function count(array $filters = []): int;
}