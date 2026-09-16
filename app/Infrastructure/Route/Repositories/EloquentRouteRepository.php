<?php

namespace App\Infrastructure\Route\Repositories;

use App\Domain\Route\Entities\Route;
use App\Domain\Route\Repositories\RouteRepositoryInterface;
use App\Infrastructure\Route\Models\RouteModel;
use App\Infrastructure\Route\Mappers\RouteMapper;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentRouteRepository implements RouteRepositoryInterface
{
    public function findById(int $id): ?Route
    {
        $model = RouteModel::with(['school', 'provider', 'driver'])->find($id);
        return $model ? RouteMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = RouteModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => RouteMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?Route
    {
        $model = RouteModel::where($field, $value)->first();
        return $model ? RouteMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = RouteModel::where($field, $value)->get();
        return $models->map(fn($model) => RouteMapper::toArray(RouteMapper::toDomain($model)))->toArray();
    }

    public function save(Route $entity): void
    {
        $data = RouteMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = RouteModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new RouteModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        RouteModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return RouteModel::where('id', $id)->exists();
    }

    public function existsByProviderAndName(int $provider_id, string $name): bool
    {
        return RouteModel::where('fk_provider', $provider_id)->where('name', $name)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = RouteModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }
}