<?php

namespace App\Infrastructure\TripStudent\Repositories;

use App\Domain\TripStudent\Entities\TripStudent;
use App\Domain\TripStudent\Repositories\TripStudentRepositoryInterface;
use App\Infrastructure\TripStudent\Models\TripStudentModel;
use App\Infrastructure\TripStudent\Mappers\TripStudentMapper;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentTripStudentRepository implements TripStudentRepositoryInterface
{
    public function findById(int $id): ?TripStudent
    {
        $model = TripStudentModel::find($id);
        return $model ? TripStudentMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = TripStudentModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => TripStudentMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?TripStudent
    {
        $model = TripStudentModel::where($field, $value)->first();
        return $model ? TripStudentMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = TripStudentModel::where($field, $value)->get();
        return $models->map(fn($model) => TripStudentMapper::toDomain($model))->toArray();
    }

    public function save(TripStudent $entity): void
    {
        $data = TripStudentMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = TripStudentModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new TripStudentModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        TripStudentModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return TripStudentModel::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = TripStudentModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }
}