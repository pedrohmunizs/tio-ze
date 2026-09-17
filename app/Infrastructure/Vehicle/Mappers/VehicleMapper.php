<?php

namespace App\Infrastructure\Vehicle\Mappers;

use App\Domain\Vehicle\Entities\Vehicle;
use App\Domain\Vehicle\Enums\VehicleStatus;
use App\Infrastructure\Provider\Mappers\ProviderMapper;
use App\Infrastructure\Vehicle\Models\VehicleModel;
use DateTimeImmutable;

class VehicleMapper
{
    public static function toDomain(VehicleModel $model): Vehicle
    {
        if (!$model->relationLoaded('provider')) {
            $model->load('provider');
        }

        $status = VehicleStatus::tryFrom($model->status) ?? VehicleStatus::INACTIVE;

        $entity = new Vehicle(
            brand: $model->brand,
            model: $model->model,
            plate: $model->plate,
            year: $model->year,
            capacity: $model->capacity,
            fk_provider: $model->fk_provider,
            photo: $model->photo,
            status: $status,
            id: $model->id
        );

        if ($model->relationLoaded('provider') && $model->provider) {
            $entity->loadProvider(ProviderMapper::toDomain($model->provider));
        }

        $reflection = new \ReflectionClass($entity);
        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($entity, VehicleStatus::from($model->status));

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

    public static function toArray(Vehicle $entity): array
    {
        return [
            'brand' => $entity->getBrand(),
            'model' => $entity->getModel(),
            'plate' => $entity->getPlate(),
            'year' => $entity->getYear(),
            'capacity' => $entity->getCapacity(),
            'fk_provider' => $entity->getProviderId(),
            'provider' => $entity->getProvider(),
            'photo' => $entity->getPhoto(),
            'status' => $entity->getStatus()->value,
            'status_label' => $entity->getStatus()->label(),
            'created_at' => $entity->getCreatedAt(),
        ];
    }

    public static function toModel(Vehicle $entity): array
    {
        return self::toArray($entity);
    }
}