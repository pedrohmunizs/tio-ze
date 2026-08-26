<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;

class DeleteUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(int $id): void
    {
        if (!$this->userRepository->exists($id)) {
            throw new \DomainException('User not found');
        }

        $this->userRepository->delete($id);
    }
}