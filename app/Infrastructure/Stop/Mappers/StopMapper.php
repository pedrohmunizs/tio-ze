<?php

namespace App\Infrastructure\Stop\Mappers;

use App\Domain\Stop\Entities\Stop;
use App\Domain\Stop\Enums\StopType;
use App\Infrastructure\Address\Mappers\AddressMapper;
use App\Infrastructure\Child\Mappers\ChildMapper;
use App\Infrastructure\Stop\Models\StopModel;
use DateTimeImmutable;

class StopMapper
{
    public static function toDomain(StopModel $model): Stop
    {
        $type = StopType::tryFrom($model->type) ?? StopType::GOING;

        $entity = new Stop(
            fk_route: $model->fk_route,
            fk_address: $model->fk_address,
            stop_order: $model->stop_order,
            type: $type,
            id: $model->id
        );

        if ($model->relationLoaded('student') && $model->student) {
            $entity->loadStudent(ChildMapper::toDomain($model->student));
        }

        if ($model->relationLoaded('address') && $model->address) {
            $entity->loadAddress(AddressMapper::toDomain($model->address));
        }

        $reflection = new \ReflectionClass($entity);
        $statusProperty = $reflection->getProperty('type');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($entity, StopType::from($model->type));

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

        return $entity;
    }

    public static function toArray(Stop $entity): array
    {
        return [
            'fk_route' => $entity->getRouteId(),
            'fk_address' => $entity->getAddressId(),
            'stop_order' => $entity->getStopOrder(),
            'type' => $entity->getType()->value,
            'type_label' => $entity->getType()->label(),
            'student' => $entity->getStudent()?->toArray(),
            'address' => $entity->getAddress()?->toArray(),
            'created_at' => $entity->getCreatedAt(),
        ];
    }

    public static function toModel(Stop $entity): array
    {
        return self::toArray($entity);
    }
}