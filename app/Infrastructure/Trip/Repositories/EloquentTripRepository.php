<?php

namespace App\Infrastructure\Trip\Repositories;

use App\Domain\Trip\Entities\Trip;
use App\Domain\Trip\Repositories\TripRepositoryInterface;
use App\Infrastructure\Trip\Models\TripModel;
use App\Infrastructure\Trip\Mappers\TripMapper;
use Illuminate\Pagination\LengthAwarePaginator;
use Override;

class EloquentTripRepository implements TripRepositoryInterface
{
    public function findById(int $id): ?Trip
    {
        $model = TripModel::find($id);
        return $model ? TripMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = TripModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => TripMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?Trip
    {
        $model = TripModel::where($field, $value)->first();
        return $model ? TripMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = TripModel::where($field, $value)->get();
        return $models->map(fn($model) => TripMapper::toDomain($model))->toArray();
    }

    public function save(Trip $entity): void
    {
        $data = TripMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = TripModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new TripModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        TripModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return TripModel::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = TripModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }

    public function existsByRouteAndDate(int $fk_route, string $date): bool
    {
        return TripModel::where('fk_route', $fk_route)->where('date', $date)->exists();
    }
}