<?php

namespace App\Infrastructure\Trip\Mappers;

use App\Domain\Trip\Entities\Trip;
use App\Domain\Trip\Enums\TripStatus;
use App\Domain\Trip\Enums\TripType;
use App\Infrastructure\Driver\Mappers\DriverMapper;
use App\Infrastructure\Route\Mappers\RouteMapper;
use App\Infrastructure\Trip\Models\TripModel;
use App\Infrastructure\Vehicle\Mappers\VehicleMapper;
use DateTimeImmutable;

class TripMapper
{
    public static function toDomain(TripModel $model): Trip
    {
        if (!$model->relationLoaded('route')) {
            $model->load('route');
        }

        if (!$model->relationLoaded('vehicle')) {
            $model->load('vehicle');
        }

        if (!$model->relationLoaded('driver')) {
            $model->load('driver');
        }

        $entity = new Trip(
            fk_route: $model->fk_route,
            fk_vehicle: $model->fk_vehicle,
            fk_driver: $model->fk_driver,
            date: $model->date,
            type: TripType::tryFrom($model->type),
            status: TripStatus::tryFrom($model->status) ?? TripStatus::SCHEDULED,
            id: $model->id
        );

        if ($model->relationLoaded('route') && $model->route) {
            $entity->loadRoute(RouteMapper::toDomain($model->route));
        }

        if ($model->relationLoaded('vehicle') && $model->vehicle) {
            $entity->loadVehicle(VehicleMapper::toDomain($model->vehicle));
        }

        if ($model->relationLoaded('driver') && $model->driver) {
            $entity->loadDriver(DriverMapper::toDomain($model->driver));
        }

        $reflection = new \ReflectionClass($entity);
        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($entity, TripStatus::from($model->status));

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

    public static function toArray(Trip $entity): array
    {
        return [
            'id' => $entity->getId(),
            'fk_route' => $entity->getRouteId(),
            'fk_vehicle' => $entity->getVehicleId(),
            'fk_driver' => $entity->getDriverId(),
            'date' => $entity->getDate(),
            'type' => $entity->getType()->value,
            'type_label' => $entity->getType()->label(),
            'status' => $entity->getStatus()->value,
            'status_label' => $entity->getStatus()->label(),
            'started_at' => $entity->getStartedAt(),
            'completed_at' => $entity->getCompletedAt(),
            'created_at' => $entity->getCreatedAt(),
        ];
    }

    public static function toModel(Trip $entity): array
    {
        return self::toArray($entity);
    }
}