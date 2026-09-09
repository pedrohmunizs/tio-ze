<?php

namespace App\Infrastructure\Vehicle\Repositories;

use App\Domain\Vehicle\Entities\Vehicle;
use App\Domain\Vehicle\Repositories\VehicleRepositoryInterface;
use App\Infrastructure\Vehicle\Models\VehicleModel;
use App\Infrastructure\Vehicle\Mappers\VehicleMapper;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentVehicleRepository implements VehicleRepositoryInterface
{
    public function findById(int $id): ?Vehicle
    {
        $model = VehicleModel::with(['provider'])->find($id);
        return $model ? VehicleMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = VehicleModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => VehicleMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?Vehicle
    {
        $model = VehicleModel::where($field, $value)->first();
        return $model ? VehicleMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = VehicleModel::where($field, $value)->get();
        return $models->map(fn($model) => VehicleMapper::toDomain($model))->toArray();
    }

    public function save(Vehicle $entity): void
    {
        $data = VehicleMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = VehicleModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new VehicleModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        VehicleModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return VehicleModel::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = VehicleModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }
}