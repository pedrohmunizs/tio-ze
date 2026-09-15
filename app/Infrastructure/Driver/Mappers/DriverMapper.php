<?php

namespace App\Infrastructure\Driver\Mappers;

use App\Domain\Driver\Entities\Driver;
use App\Domain\Driver\Enums\DriverStatus;
use App\Infrastructure\Driver\Models\DriverModel;
use App\Infrastructure\Route\Mappers\RouteMapper;
use DateTimeImmutable;

class DriverMapper
{
    public static function toDomain(DriverModel $model): Driver
    {
        if (!$model->relationLoaded('routes')) {
            $model->load('routes');
        }

        $status = DriverStatus::tryFrom($model->status) ?? DriverStatus::PENDING;

        $entity = new Driver(
            userId: $model->fk_user,
            providerId: $model->fk_provider,
            isAutonomous: $model->is_autonomous,
            status: $status,
            licenseNumber: $model->license_number,
            licenseCategory: $model->license_category,
            licenseValidUntil: $model->license_valid_until,
            id: $model->id
        );

        if ($model->relationLoaded('routes') && $model->routes->isNotEmpty()) {
            $routes = $model->routes
                ->map(fn($route) => RouteMapper::toArray(
                    RouteMapper::toDomain($route)
                ))
                ->toArray();

            $entity->loadRoutes($routes);
        }

        $reflection = new \ReflectionClass($entity);
        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($entity, DriverStatus::from($model->status));

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

    public static function toArray(Driver $entity): array
    {
        return [
            'license_number' => $entity->getLicenseNumber(),
            'license_category' => $entity->getLicenseCategory(),
            'license_valid_until' => $entity->getLicenseValidUntil(),
            'status' => $entity->getStatus()->value,
            'is_autonomous' => $entity->getIsAutonomous(),
            'fk_provider' => $entity->getProviderId(),
            'fk_user' => $entity->getUserId(),
        ];
    }

    public static function toModel(Driver $entity): array
    {
        return self::toArray($entity);
    }
}