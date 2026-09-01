<?php

namespace App\Infrastructure\Contract\Mappers;

use App\Domain\Contract\Entities\Contract;
use App\Domain\Contract\Enums\ContractStatus;
use App\Infrastructure\Contract\Models\ContractModel;
use DateTimeImmutable;

class ContractMapper
{
    public static function toDomain(ContractModel $model): Contract
    {
        $status = ContractStatus::tryFrom($model->status) ?? ContractStatus::PENDING;

        $entity = new Contract(
            $model->fk_student,
            $model->fk_route,
            $model->fk_provider,
            $model->fk_transport_request,
            $model->start_date,
            $model->end_date,
            $status,
            $model->id
        );

        $reflection = new \ReflectionClass($entity);
        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($entity, ContractStatus::from($model->status));

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

    public static function toArray(Contract $entity): array
    {
        return [
            'start_date' => $entity->getStartDate(),
            'end_date' => $entity->getEndDate(),
            'status' => $entity->getStatus()->value,
            'fk_route' => $entity->getRouteId(),
            'fk_provider' => $entity->getProviderId(),
            'fk_student' => $entity->getStudentId(),
            'fk_transport_request' => $entity->getTransportRequestId(),
            'created_at' => $entity->getCreatedAt(),
        ];
    }

    public static function toModel(Contract $entity): array
    {
        return self::toArray($entity);
    }
}