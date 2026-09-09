<?php

namespace App\Infrastructure\Stop\Repositories;

use App\Domain\Stop\Entities\Stop;
use App\Domain\Stop\Repositories\StopRepositoryInterface;
use App\Infrastructure\Stop\Models\StopModel;
use App\Infrastructure\Stop\Mappers\StopMapper;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Override;

class EloquentStopRepository implements StopRepositoryInterface
{
    public function findById(int $id): ?Stop
    {
        $model = StopModel::with(['student', 'student.address', 'address'])->find($id);
        return $model ? StopMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = StopModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => StopMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?Stop
    {
        $model = StopModel::where($field, $value)->first();
        return $model ? StopMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = StopModel::where($field, $value)->get();
        return $models->map(fn($model) => StopMapper::toDomain($model))->toArray();
    }

    public function save(Stop $entity): void
    {
        $data = StopMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = StopModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new StopModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        StopModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return StopModel::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = StopModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }

    public function findByRouteId(int $fk_route, string $type): array
    {
        return StopModel::with(['student', 'student.address', 'address'])
            ->where('fk_route', $fk_route)
            ->where('type', $type)
            ->orderBy('stop_order')
            ->get()
            ->toArray();
    }

    public function updateOrder(int $fk_student, int $stop_order, int $fk_route, string $type): void
    {
        $stop = StopModel::where('fk_route', $fk_route)
            ->where('type', $type)
            ->whereHas('stopChildren', function ($query) use ($fk_student) {
                $query->where('fk_child', $fk_student);
            })
            ->first();

        if ($stop) {
            $stop->update(['stop_order' => $stop_order]);
        }
    }

    public function getMaxOrder(int $fk_route, string $type): int
    {
        $max = StopModel::where('fk_route', $fk_route)->where('type', $type)->max('stop_order');
        return $max ?? 0;
    }
}