<?php

namespace App\Infrastructure\Provider\Repositories;

use App\Domain\Provider\Entities\Provider;
use App\Domain\Provider\Repositories\ProviderRepositoryInterface;
use App\Infrastructure\Provider\Models\ProviderModel;
use App\Infrastructure\Provider\Mappers\ProviderMapper;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentProviderRepository implements ProviderRepositoryInterface
{
    public function findById(int $id): ?Provider
    {
        $model = ProviderModel::find($id);
        return $model ? ProviderMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = ProviderModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => ProviderMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?Provider
    {
        $model = ProviderModel::where($field, $value)->first();
        return $model ? ProviderMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = ProviderModel::where($field, $value)->get();
        return $models->map(fn($model) => ProviderMapper::toDomain($model))->toArray();
    }

    public function save(Provider $entity): void
    {
        $data = ProviderMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = ProviderModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new ProviderModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        ProviderModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return ProviderModel::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = ProviderModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }
}