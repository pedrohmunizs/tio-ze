<?php

namespace App\Infrastructure\School\Mappers;

use App\Domain\School\Entities\School;
use App\Domain\School\Enums\SchoolStatus;
use App\Infrastructure\School\Models\SchoolModel;
use DateTimeImmutable;

class SchoolMapper
{
    public static function toDomain(SchoolModel $model): School
    {

        $entity = new School(
            name: $model->name,
            phone: $model->phone,
            addressId: $model->fk_address,
            id: $model->id
        );

        $reflection = new \ReflectionClass($entity);
        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($entity, SchoolStatus::from($model->status));

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

    public static function toArray(School $entity): array
    {
        return [
            'name' => $entity->getName(),
            'phone' => $entity->getPhone(),
            'status' => $entity->getStatus()->value,
            'fk_address' => $entity->getAddressId(),
        ];
    }

    public static function toModel(School $entity): array
    {
        return self::toArray($entity);
    }
}