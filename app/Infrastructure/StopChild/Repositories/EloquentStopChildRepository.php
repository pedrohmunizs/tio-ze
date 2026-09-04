<?php

namespace App\Infrastructure\StopChild\Repositories;

use App\Domain\StopChild\Entities\StopChild;
use App\Domain\StopChild\Repositories\StopChildRepositoryInterface;
use App\Infrastructure\StopChild\Models\StopChildModel;
use App\Infrastructure\StopChild\Mappers\StopChildMapper;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentStopChildRepository implements StopChildRepositoryInterface
{
    public function findById(int $id): ?StopChild
    {
        $model = StopChildModel::find($id);
        return $model ? StopChildMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = StopChildModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => StopChildMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?StopChild
    {
        $model = StopChildModel::where($field, $value)->first();
        return $model ? StopChildMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = StopChildModel::where($field, $value)->get();
        return $models->map(fn($model) => StopChildMapper::toDomain($model))->toArray();
    }

    public function save(StopChild $entity): void
    {
        $data = StopChildMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = StopChildModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new StopChildModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        StopChildModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return StopChildModel::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = StopChildModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }
}