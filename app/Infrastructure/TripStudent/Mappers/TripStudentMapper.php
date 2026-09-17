<?php

namespace App\Infrastructure\TripStudent\Mappers;

use App\Domain\TripStudent\Entities\TripStudent;
use App\Domain\TripStudent\Enums\TripStudentStatus;
use App\Infrastructure\Child\Models\ChildModel;
use App\Infrastructure\Trip\Mappers\TripMapper;
use App\Infrastructure\TripStudent\Models\TripStudentModel;
use DateTimeImmutable;

class TripStudentMapper
{
    public static function toDomain(TripStudentModel $model): TripStudent
    {
        if (!$model->relationLoaded('trip')) {
            $model->load('trip');
        }

        if (!$model->relationLoaded('student')) {
            $model->load('student');
        }

        $entity = new TripStudent(
            fk_trip: $model->fk_trip,
            fk_student: $model->fk_student,
            pickup_time: $model->pickup_time,
            dropoff_time: $model->dropoff_time,
            status: TripStudentStatus::tryFrom($model->status) ?? TripStudentStatus::PENDING,
            id: $model->id
        );

        if ($model->relationLoaded('trip') && $model->trip) {
            $entity->loadTrip(TripMapper::toDomain($model->trip));
        }

        if ($model->relationLoaded('student') && $model->student) {
            $entity->loadStudent(ChildModel::toDomain($model->student));
        }

        $reflection = new \ReflectionClass($entity);
        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($entity, TripStudentStatus::from($model->status));

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

    public static function toArray(TripStudent $entity): array
    {
        return [
            'id' => $entity->getId(),
            'fk_trip' => $entity->getTripId(),
            'fk_student' => $entity->getStudentId(),
            'pickup_time' => $entity->getPickupTime(),
            'dropoff_time' => $entity->getDropOffTime(),
            'status' => $entity->getStatus()->value,
            'status_label' => $entity->getStatus()->label(),
            'created_at' => $entity->getCreatedAt(),
        ];
    }

    public static function toModel(TripStudent $entity): array
    {
        return self::toArray($entity);
    }
}