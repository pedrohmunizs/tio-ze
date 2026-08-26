<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;

class GetUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->userRepository->findAll($filters, $page, $perPage);
    }
}