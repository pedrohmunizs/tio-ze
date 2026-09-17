<?php

namespace App\Domain\TripStudent\Repositories;

use App\Domain\TripStudent\Entities\TripStudent;

interface TripStudentRepositoryInterface
{
    public function findById(int $id): ?TripStudent;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array;
    public function findByField(string $field, mixed $value): ?TripStudent;
    public function findManyByField(string $field, mixed $value): array;
    public function save(TripStudent $entity): void;
    public function delete(int $id): void;
    public function exists(int $id): bool;
    public function count(array $filters = []): int;
}