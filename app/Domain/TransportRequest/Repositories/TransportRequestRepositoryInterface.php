<?php

namespace App\Domain\TransportRequest\Repositories;

use App\Domain\TransportRequest\Entities\TransportRequest;

interface TransportRequestRepositoryInterface
{
    public function findById(int $id): ?TransportRequest;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array;
    public function findByField(string $field, mixed $value): ?TransportRequest;
    public function findManyByField(string $field, mixed $value): array;
    public function save(TransportRequest $entity): void;
    public function delete(int $id): void;
    public function exists(int $id): bool;
    public function count(array $filters = []): int;
}