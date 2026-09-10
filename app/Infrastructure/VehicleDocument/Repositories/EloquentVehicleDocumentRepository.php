<?php

namespace App\Infrastructure\VehicleDocument\Repositories;

use App\Domain\VehicleDocument\Entities\VehicleDocument;
use App\Domain\VehicleDocument\Repositories\VehicleDocumentRepositoryInterface;
use App\Infrastructure\VehicleDocument\Models\VehicleDocumentModel;
use App\Infrastructure\VehicleDocument\Mappers\VehicleDocumentMapper;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentVehicleDocumentRepository implements VehicleDocumentRepositoryInterface
{
    public function findById(int $id): ?VehicleDocument
    {
        $model = VehicleDocumentModel::find($id);
        return $model ? VehicleDocumentMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = VehicleDocumentModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => VehicleDocumentMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?VehicleDocument
    {
        $model = VehicleDocumentModel::where($field, $value)->first();
        return $model ? VehicleDocumentMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = VehicleDocumentModel::where($field, $value)->get();
        return $models->map(fn($model) => VehicleDocumentMapper::toDomain($model))->toArray();
    }

    public function save(VehicleDocument $entity): void
    {
        $data = VehicleDocumentMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = VehicleDocumentModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new VehicleDocumentModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        VehicleDocumentModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return VehicleDocumentModel::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = VehicleDocumentModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }
}