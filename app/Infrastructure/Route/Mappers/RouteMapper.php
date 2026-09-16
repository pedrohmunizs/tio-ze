<?php

namespace App\Infrastructure\Route\Mappers;

use App\Domain\Route\Entities\Route;
use App\Domain\Route\Enums\RouteStatus;
use App\Domain\Route\ValueObjects\DaysOfWeek;
use App\Domain\Route\ValueObjects\Price;
use App\Domain\Route\ValueObjects\Time;
use App\Infrastructure\Driver\Mappers\DriverMapper;
use App\Infrastructure\Provider\Mappers\ProviderMapper;
use App\Infrastructure\Route\Models\RouteModel;
use App\Infrastructure\School\Mappers\SchoolMapper;
use DateTimeImmutable;

class RouteMapper
{
    public static function toDomain(RouteModel $model): Route
    {
        $price = new Price((float) $model->price);
        $goingTime = new Time($model->going_time);
        $returningTime = new Time($model->returning_time);
        $daysOfWeek = new DaysOfWeek($model->days_of_week);

        $entity = new Route(
            name: $model->name,
            price: $price,
            goingTime: $goingTime,
            returningTime: $returningTime,
            daysOfWeek: $daysOfWeek,
            schoolId: (int) $model->fk_school,
            providerId: (int) $model->fk_provider,
            driverId: (int) $model->fk_driver,
            vehicleId: (int) $model->fk_vehicle,
            status: RouteStatus::from($model->status),
            id: $model->id,
        );

        if ($model->relationLoaded('school') && $model->school) {
            $entity->loadSchool(SchoolMapper::toDomain($model->school));
        }

        if ($model->relationLoaded('driver') && $model->driver) {
            $entity->loadDriver(DriverMapper::toDomain($model->driver));
        }

        if ($model->relationLoaded('provider') && $model->provider) {
            $entity->loadProvider(ProviderMapper::toDomain($model->provider));
        }

        $reflection = new \ReflectionClass($entity);
        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($entity, RouteStatus::from($model->status));

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

    public static function toArray(Route $entity): array
    {
        return [
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'price' => $entity->getPrice()->getValue(),
            'going_time' => $entity->getGoingTime()->getValue(),
            'returning_time' => $entity->getReturningTime()->getValue(),
            'days_of_week' => $entity->getDaysOfWeek()->toArray(),
            'fk_school' => $entity->getSchoolId(),
            'fk_provider' => $entity->getProviderId(),
            'fk_driver' => $entity->getDriverId(),
            'fk_vehicle' => $entity->getVehicleId(),
            'status' => $entity->getStatus()->value,
        ];
    }

    public static function toModel(Route $entity): array
    {
        return self::toArray($entity);
    }
}