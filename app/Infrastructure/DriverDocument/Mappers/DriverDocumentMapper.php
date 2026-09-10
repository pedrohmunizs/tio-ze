<?php

namespace App\Infrastructure\DriverDocument\Mappers;

use App\Domain\DriverDocument\Entities\DriverDocument;
use App\Domain\DriverDocument\Enums\DriverDocumentStatus;
use App\Domain\DriverDocument\Enums\DriverDocumentType;
use App\Infrastructure\Driver\Mappers\DriverMapper;
use App\Infrastructure\DriverDocument\Models\DriverDocumentModel;
use DateTimeImmutable;

class DriverDocumentMapper
{
    public static function toDomain(DriverDocumentModel $model): DriverDocument
    {
        if (!$model->relationLoaded('driver')) {
            $model->load('driver');
        }

        $type = DriverDocumentType::tryFrom($model->type);
        $status = DriverDocumentStatus::tryFrom($model->status) ?? DriverDocumentStatus::PENDING;

        $entity = new DriverDocument(
            fk_driver: $model->fk_driver,
            file_path: $model->file_path,
            valid_until: $model->valid_until,
            type: $type,
            status: $status,
            id: $model->id,
        );

        if ($model->relationLoaded('driver') && $model->driver) {
            $entity->loadDriver(DriverMapper::toDomain($model->driver));
        }

        $reflection = new \ReflectionClass($entity);
        $statusProperty = $reflection->getProperty('status');
        $typeProperty = $reflection->getProperty('type');
        $statusProperty->setAccessible(true);
        $typeProperty->setAccessible(true);
        $statusProperty->setValue($entity, DriverDocumentStatus::from($model->status));
        $typeProperty->setValue($entity, DriverDocumentType::from($model->type));

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

    public static function toArray(DriverDocument $entity, ?string $temporary_url = null): array
    {
        return [
            'id' => $entity->getId(),
            'fk_driver' => $entity->getDriverId(),
            'driver' => $entity->getDriver(),
            'file_path' => $entity->getFilePath(),
            'file_url' => $temporary_url,
            'valid_until' => $entity->getValidUntil(),
            'type' => $entity->getType()->value,
            'type_label' => $entity->getType()->label(),
            'status' => $entity->getStatus()->value,
            'status_label' => $entity->getStatus()->label(),
            'created_at' => $entity->getCreatedAt(),
        ];
    }

    public static function toModel(DriverDocument $entity): array
    {
        return self::toArray($entity);
    }
}