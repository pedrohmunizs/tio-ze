<?php

namespace App\Infrastructure\VehicleDocument\Mappers;

use App\Domain\VehicleDocument\Entities\VehicleDocument;
use App\Domain\VehicleDocument\Enums\VehicleDocumentStatus;
use App\Domain\VehicleDocument\Enums\VehicleDocumentType;
use App\Infrastructure\Vehicle\Mappers\VehicleMapper;
use App\Infrastructure\VehicleDocument\Models\VehicleDocumentModel;
use DateTimeImmutable;

class VehicleDocumentMapper
{
    public static function toDomain(VehicleDocumentModel $model): VehicleDocument
    {
        if (!$model->relationLoaded('vehicle')) {
            $model->load('vehicle');
        }

        $type = VehicleDocumentType::tryFrom($model->type);
        $status = VehicleDocumentStatus::tryFrom($model->status) ?? VehicleDocumentStatus::PENDING;

        $entity = new VehicleDocument(
            fk_vehicle: $model->fk_vehicle,
            file_path: $model->file_path,
            type: $type,
            status: $status,
            id: $model->id,
        );

        if ($model->relationLoaded('vehicle') && $model->vehicle) {
            $entity->loadVehicle(VehicleMapper::toDomain($model->vehicle));
        }

        $reflection = new \ReflectionClass($entity);
        $statusProperty = $reflection->getProperty('status');
        $typeProperty = $reflection->getProperty('type');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($entity, VehicleDocumentStatus::from($model->status));
        $typeProperty->setAccessible(true);
        $typeProperty->setValue($entity, VehicleDocumentType::from($model->type));

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

    public static function toArray(VehicleDocument $entity, ?string $temporaryUrl = null): array
    {
        return [
            'id' => $entity->getId(),
            'fk_vehicle' => $entity->getVehicleId(),
            'vehicle' => $entity->getVehicle(),
            'file_path' => $entity->getFilePath(),
            'file_url' => $temporaryUrl,
            'type' => $entity->getType()->value,
            'type_label' => $entity->getType()->label(),
            'status' => $entity->getStatus()->value,
            'status_label' => $entity->getStatus()->label(),
            'created_at' => $entity->getCreatedAt(),
        ];
    }

    public static function toModel(VehicleDocument $entity): array
    {
        return self::toArray($entity);
    }
}