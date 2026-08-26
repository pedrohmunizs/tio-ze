<?php

namespace App\Infrastructure\User\Repositories;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\CPF;
use App\Domain\User\ValueObjects\Email;
use App\Infrastructure\User\Mappers\UserMapper;
use App\Infrastructure\User\Models\UserModel;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        $model = UserModel::find($id);
        return $model ? UserMapper::toDomain($model) : null;
    }

    public function findByEmail(Email $email): ?User
    {
        $model = UserModel::where('email', $email->getValue())->first();
        return $model ? UserMapper::toDomain($model) : null;
    }

    public function findByCpf(Cpf $cpf): ?User
    {
        $model = UserModel::where('cpf', $cpf->getValue())->first();
        return $model ? UserMapper::toDomain($model) : null;
    }

    public function findAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $query = UserModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(
                fn($model) => UserMapper::toDomain($model),
                $paginator->items()
            ),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    public function findByField(string $field, mixed $value): ?User
    {
        $model = UserModel::where($field, $value)->first();
        return $model ? UserMapper::toDomain($model) : null;
    }

    public function findManyByField(string $field, mixed $value): array
    {
        $models = UserModel::where($field, $value)->get();
        return $models->map(fn($model) => UserMapper::toDomain($model))->toArray();
    }

    public function save(User $entity): void
    {
        $data = UserMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = UserModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new UserModel($data);
        $model->save();
        $entity->setId($model->id);
    }

    public function delete(int $id): void
    {
        UserModel::destroy($id);
    }

    public function existsByEmail(Email $email): bool
    {
        return UserModel::where('email', $email->getValue())->exists();
    }

    public function existsByCpf(CPF $cpf): bool
    {
        return UserModel::where('cpf', $cpf->getValue())->exists();
    }

    public function exists(int $id): bool
    {
        return UserModel::where('id', $id)->exists();
    }

    public function count(array $filters = []): int
    {
        $query = UserModel::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->count();
    }
}