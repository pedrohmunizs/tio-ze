<?php

namespace App\Infrastructure\User\Mappers;

use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserStatus;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\CPF;
use App\Infrastructure\User\Models\UserModel;
use DateTimeImmutable;

class UserMapper
{
    public static function toDomain(UserModel $model): User
    {
        $user = new User(
            name: $model->name,
            email: new Email($model->email),
            cpf: new CPF($model->cpf),
            phone: $model->phone,
            password: $model->password, // Já está hasheada
            addressId: $model->fk_address,
            id: $model->id,
        );

        // Atualizar status
        $reflection = new \ReflectionClass($user);
        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($user, UserStatus::from($model->status));

        // Atualizar timestamps
        if ($model->created_at) {
            $createdAtProperty = $reflection->getProperty('createdAt');
            $createdAtProperty->setAccessible(true);
            $createdAtProperty->setValue($user, new DateTimeImmutable($model->created_at));
        }

        if ($model->updated_at) {
            $updatedAtProperty = $reflection->getProperty('updatedAt');
            $updatedAtProperty->setAccessible(true);
            $updatedAtProperty->setValue($user, new DateTimeImmutable($model->updated_at));
        }

        if ($model->deleted_at) {
            $deletedAtProperty = $reflection->getProperty('deletedAt');
            $deletedAtProperty->setAccessible(true);
            $deletedAtProperty->setValue($user, new DateTimeImmutable($model->deleted_at));
        }

        return $user;
    }

    public static function toArray(User $user): array
    {
        return [
            'name' => $user->getName(),
            'email' => $user->getEmail()->getValue(),
            'cpf' => $user->getCpf()->getValue(),
            'phone' => $user->getPhone(),
            'password' => $user->getPasswordHash(),
            'status' => $user->getStatus()->value,
            'fk_address' => $user->getAddressId(),
        ];
    }

    public static function toModel(User $user): array
    {
        return self::toArray($user);
    }
}