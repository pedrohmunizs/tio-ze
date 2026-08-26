<?php

namespace App\Domain\Address\Repositories;

use App\Domain\Address\Entities\Address;

interface AddressRepositoryInterface
{
    public function findById(int $id): ?Address;
    public function save(Address $entity);
    public function delete(int $id): void;
    public function exists(int $id): bool;
}