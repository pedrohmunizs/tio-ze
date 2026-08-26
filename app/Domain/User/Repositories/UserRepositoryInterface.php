<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\CPF;
use App\Domain\User\ValueObjects\Email;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function findByEmail(Email $email): ?User;
    public function findByCpf(CPF $cpf): ?User;
    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array;
    public function findByField(string $field, mixed $value): ?User;
    public function findManyByField(string $field, mixed $value): array;
    public function save(User $entity): void;
    public function delete(int $id): void;
    public function existsByEmail(Email $email): bool;
    public function existsByCpf(Cpf $cpf): bool;
    public function exists(int $id): bool;
    public function count(array $filters = []): int;
}