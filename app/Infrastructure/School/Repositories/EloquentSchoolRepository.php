<?php

namespace App\Infrastructure\School\Repositories;

use App\Domain\School\Entities\School;
use App\Domain\School\Repositories\SchoolRepositoryInterface;
use App\Infrastructure\School\Models\SchoolModel;
use App\Infrastructure\School\Mappers\SchoolMapper;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentSchoolRepository implements SchoolRepositoryInterface
{
    public function findById(int $id): ?School
    {
        $model = SchoolModel::find($id);
        return $model ? SchoolMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = SchoolModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => SchoolMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?School
    {
        $model = SchoolModel::where($field, $value)->first();
        return $model ? SchoolMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = SchoolModel::where($field, $value)->get();
        return $models->map(fn($model) => SchoolMapper::toDomain($model))->toArray();
    }

    public function save(School $entity): void
    {
        $data = SchoolMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = SchoolModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new SchoolModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        SchoolModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return SchoolModel::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = SchoolModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }
}