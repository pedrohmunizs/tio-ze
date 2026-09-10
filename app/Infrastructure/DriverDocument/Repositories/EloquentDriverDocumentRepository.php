<?php

namespace App\Infrastructure\DriverDocument\Repositories;

use App\Domain\DriverDocument\Entities\DriverDocument;
use App\Domain\DriverDocument\Repositories\DriverDocumentRepositoryInterface;
use App\Infrastructure\DriverDocument\Models\DriverDocumentModel;
use App\Infrastructure\DriverDocument\Mappers\DriverDocumentMapper;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentDriverDocumentRepository implements DriverDocumentRepositoryInterface
{
    public function findById(int $id): ?DriverDocument
    {
        $model = DriverDocumentModel::find($id);
        return $model ? DriverDocumentMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = DriverDocumentModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => DriverDocumentMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?DriverDocument
    {
        $model = DriverDocumentModel::where($field, $value)->first();
        return $model ? DriverDocumentMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = DriverDocumentModel::where($field, $value)->get();
        return $models->map(fn($model) => DriverDocumentMapper::toDomain($model))->toArray();
    }

    public function save(DriverDocument $entity): void
    {
        $data = DriverDocumentMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = DriverDocumentModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new DriverDocumentModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        DriverDocumentModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return DriverDocumentModel::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = DriverDocumentModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }
}