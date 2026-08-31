<?php

namespace App\Infrastructure\TransportRequest\Mappers;

use App\Domain\TransportRequest\Entities\TransportRequest;
use App\Domain\TransportRequest\Enums\TransportRequestStatus;
use App\Infrastructure\TransportRequest\Models\TransportRequestModel;
use DateTimeImmutable;

class TransportRequestMapper
{
    public static function toDomain(TransportRequestModel $model): TransportRequest
    {
        $status = TransportRequestStatus::tryFrom($model->status) ?? TransportRequestStatus::PENDING;
        
        $entity = new TransportRequest(
            $model->fk_route,
            $model->fk_provider,
            $model->fk_student,
            $model->message,
            $status,
            $model->id,
        );

        $reflection = new \ReflectionClass($entity);
        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($entity, TransportRequestStatus::from($model->status));

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

    public static function toArray(TransportRequest $entity): array
    {
        return [
            'message' => $entity->getMessage(),
            'status' => $entity->getStatus()->value,
            'fk_route' => $entity->getRouteId(),
            'fk_provider' => $entity->getProviderId(),
            'fk_student' => $entity->getStudentId(),
            'created_at' => $entity->getCreatedAt(),
        ];
    }

    public static function toModel(TransportRequest $entity): array
    {
        return self::toArray($entity);
    }
}