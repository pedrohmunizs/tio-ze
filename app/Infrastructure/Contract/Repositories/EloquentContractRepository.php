<?php

namespace App\Infrastructure\Contract\Repositories;

use App\Domain\Contract\Entities\Contract;
use App\Domain\Contract\Repositories\ContractRepositoryInterface;
use App\Infrastructure\Contract\Models\ContractModel;
use App\Infrastructure\Contract\Mappers\ContractMapper;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentContractRepository implements ContractRepositoryInterface
{
    public function findById(int $id): ?Contract
    {
        $model = ContractModel::find($id);
        return $model ? ContractMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = ContractModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => ContractMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?Contract
    {
        $model = ContractModel::where($field, $value)->first();
        return $model ? ContractMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = ContractModel::where($field, $value)->get();
        return $models->map(fn($model) => ContractMapper::toDomain($model))->toArray();
    }

    public function save(Contract $entity): void
    {
        $data = ContractMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = ContractModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new ContractModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        ContractModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return ContractModel::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = ContractModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }
}