<?php

namespace App\Infrastructure\TransportRequest\Repositories;

use App\Domain\TransportRequest\Entities\TransportRequest;
use App\Domain\TransportRequest\Repositories\TransportRequestRepositoryInterface;
use App\Infrastructure\TransportRequest\Models\TransportRequestModel;
use App\Infrastructure\TransportRequest\Mappers\TransportRequestMapper;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentTransportRequestRepository implements TransportRequestRepositoryInterface
{
    public function findById(int $id): ?TransportRequest
    {
        $model = TransportRequestModel::find($id);
        return $model ? TransportRequestMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = TransportRequestModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => TransportRequestMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?TransportRequest
    {
        $model = TransportRequestModel::where($field, $value)->first();
        return $model ? TransportRequestMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = TransportRequestModel::where($field, $value)->get();
        return $models->map(fn($model) => TransportRequestMapper::toDomain($model))->toArray();
    }

    public function save(TransportRequest $entity): void
    {
        $data = TransportRequestMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = TransportRequestModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new TransportRequestModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        TransportRequestModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return TransportRequestModel::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = TransportRequestModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }
}