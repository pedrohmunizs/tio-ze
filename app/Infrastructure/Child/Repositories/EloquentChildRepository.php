<?php

namespace App\Infrastructure\Child\Repositories;

use App\Domain\Child\Entities\Child;
use App\Domain\Child\Repositories\ChildRepositoryInterface;
use App\Infrastructure\Child\Models\ChildModel;
use App\Infrastructure\Child\Mappers\ChildMapper;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentChildRepository implements ChildRepositoryInterface
{
    public function findById(int $id): ?Child
    {
        $model = ChildModel::find($id);
        return $model ? ChildMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = ChildModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => ChildMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?Child
    {
        $model = ChildModel::where($field, $value)->first();
        return $model ? ChildMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = ChildModel::where($field, $value)->get();
        return $models->map(fn($model) => ChildMapper::toDomain($model))->toArray();
    }

    public function save(Child $entity): void
    {
        $data = ChildMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = ChildModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new ChildModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        ChildModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return ChildModel::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = ChildModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }
}