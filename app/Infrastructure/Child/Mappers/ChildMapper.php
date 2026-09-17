<?php

namespace App\Infrastructure\Child\Mappers;

use App\Domain\Child\Entities\Child;
use App\Domain\Child\Enums\ChildStatus;
use App\Infrastructure\Address\Mappers\AddressMapper;
use App\Infrastructure\Child\Models\ChildModel;
use App\Infrastructure\StopChild\Mappers\StopChildMapper;
use DateTimeImmutable;

class ChildMapper
{
    public static function toDomain(ChildModel $model): Child
    {
        if (!$model->relationLoaded('address')) {
            $model->load('address');
            $model->load('stopChildren');
        }

        $entity = new Child(
            name: $model->name,
            phone: $model->phone,
            grade: $model->grade,
            addressId: $model->fk_address,
            parentId: $model->fk_parent,
            schoolId: $model->fk_school,
            id: $model->id
        );

        if ($model->relationLoaded('address') && $model->address) {
            $entity->loadAddress(AddressMapper::toDomain($model->address));
        }

        if ($model->relationLoaded('stopChildren') && $model->stopChildren->isNotEmpty()) {
            $stopChildren = $model->stopChildren
                ->map(fn($stopChild) => StopChildMapper::toArray(
                    StopChildMapper::toDomain($stopChild)
                ))
                ->toArray();

            $entity->loadStopChildren($stopChildren);
        }

        $reflection = new \ReflectionClass($entity);
        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($entity, ChildStatus::from($model->status));

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

    public static function toArray(Child $entity): array
    {
        return [
            // 'id' => $entity->getId(),
            'name' => $entity->getName(),
            'grade' => $entity->getGrade(),
            'phone' => $entity->getPhone(),
            'status' => $entity->getStatus()->value,
            'fk_address' => $entity->getAddressId(),
            'fk_parent' => $entity->getParentId(),
            'fk_school' => $entity->getSchoolId(),
        ];
    }

    public static function toModel(Child $entity): array
    {
        return self::toArray($entity);
    }
}