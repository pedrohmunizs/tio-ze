<?php

namespace App\Infrastructure\Driver\Repositories;

use App\Domain\Driver\Entities\Driver;
use App\Domain\Driver\Repositories\DriverRepositoryInterface;
use App\Infrastructure\Driver\Models\DriverModel;
use App\Infrastructure\Driver\Mappers\DriverMapper;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentDriverRepository implements DriverRepositoryInterface
{
    public function findById(int $id): ?Driver
    {
        $model = DriverModel::find($id);
        return $model ? DriverMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = DriverModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => DriverMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?Driver
    {
        $model = DriverModel::where($field, $value)->first();
        return $model ? DriverMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = DriverModel::where($field, $value)->get();
        return $models->map(fn($model) => DriverMapper::toDomain($model))->toArray();
    }

    public function save(Driver $entity): void
    {
        $data = DriverMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = DriverModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new DriverModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        DriverModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return DriverModel::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = DriverModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }
}