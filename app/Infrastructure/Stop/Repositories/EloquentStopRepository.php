<?php

namespace App\Infrastructure\Stop\Repositories;

use App\Domain\Stop\Entities\Stop;
use App\Domain\Stop\Repositories\StopRepositoryInterface;
use App\Infrastructure\Stop\Models\StopModel;
use App\Infrastructure\Stop\Mappers\StopMapper;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentStopRepository implements StopRepositoryInterface
{
    public function findById(int $id): ?Stop
    {
        $model = StopModel::find($id);
        return $model ? StopMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = StopModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => StopMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?Stop
    {
        $model = StopModel::where($field, $value)->first();
        return $model ? StopMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = StopModel::where($field, $value)->get();
        return $models->map(fn($model) => StopMapper::toDomain($model))->toArray();
    }

    public function save(Stop $entity): void
    {
        $data = StopMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = StopModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new StopModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        StopModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return StopModel::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = StopModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }
}