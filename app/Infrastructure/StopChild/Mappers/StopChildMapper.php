<?php

namespace App\Infrastructure\StopChild\Mappers;

use App\Domain\StopChild\Entities\StopChild;
use App\Infrastructure\StopChild\Models\StopChildModel;
use DateTimeImmutable;

class StopChildMapper
{
    public static function toDomain(StopChildModel $model): StopChild
    {
        $entity = new StopChild(
            $model->fk_stop,
            $model->fk_child,
            $model->stop_order,
            $model->id
        );

        $reflection = new \ReflectionClass($entity);

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

    public static function toArray(StopChild $entity): array
    {
        return [
            'fk_stop' => $entity->getStopId(),
            'fk_child' => $entity->getChildId(),
            'stop_order' => $entity->getStopOrder(),
            'created_at' => $entity->getCreatedAt(),
        ];
    }

    public static function toModel(StopChild $entity): array
    {
        return self::toArray($entity);
    }
}