<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Application\User\DTOs\UserData;

class UpdateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(int $id, UserData $data): User
    {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            throw new \DomainException('User not found');
        }

        // Atualizar dados
        if ($data->name) {
            $user->updateProfile(
                $data->name,
                $data->phone ?? $user->getPhone(),
                $data->addressId ?? $user->getAddressId()
            );
        }

        // Atualizar status se fornecido
        if ($data->status && $data->status !== $user->getStatus()->value) {
            match($data->status) {
                'active' => $user->activate(),
                'blocked' => $user->block(),
                default => throw new \InvalidArgumentException('Invalid status'),
            };
        }

        $this->userRepository->save($user);
        return $user;
    }
}