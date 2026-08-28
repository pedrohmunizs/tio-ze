<?php

namespace App\Infrastructure\Provider\Mappers;

use App\Domain\Provider\Entities\Provider;
use App\Domain\Provider\Enums\ProviderStatus;
use App\Infrastructure\Provider\Models\ProviderModel;
use DateTimeImmutable;

class ProviderMapper
{
    public static function toDomain(ProviderModel $model): Provider
    {
        $entity = new Provider(
            name: $model->name,
            phone: $model->phone,
            userId: $model->fk_user,
            addressId: $model->fk_address,
            id: $model->id
        );

        $reflection = new \ReflectionClass($entity);
        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($entity, ProviderStatus::from($model->status));

        // Atualizar timestamps
        if ($model->created_at) {
            $createdAtProperty = $reflection->getProperty('createdAt');
            $createdAtProperty->setAccessible(true);
            $createdAtProperty->setValue($entity, new DateTimeImmutable($model->created_at));
        }

        if ($model->updated_at) {
            $updatedAtProperty = $reflection->getProperty('updatedAt');
            $updatedAtProperty->setAccessible(true);
            $updatedAtProperty->setValue($entity, new DateTimeImmutable($model->updated_at));
        }

        if ($model->deleted_at) {
            $deletedAtProperty = $reflection->getProperty('deletedAt');
            $deletedAtProperty->setAccessible(true);
            $deletedAtProperty->setValue($entity, new DateTimeImmutable($model->deleted_at));
        }

        return $entity;
    }

    public static function toArray(Provider $entity): array
    {
        return [
            'name' => $entity->getName(),
            'phone' => $entity->getPhone(),
            'description' => $entity->getDescription(),
            'rating' => $entity->getRating(),
            'status' => $entity->getStatus()->value,
            'fk_address' => $entity->getAddressId(),
            'fk_user' => $entity->getUserId(),
        ];
    }

    public static function toModel(Provider $entity): array
    {
        return self::toArray($entity);
    }
}