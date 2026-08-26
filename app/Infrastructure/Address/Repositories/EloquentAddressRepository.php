<?php

namespace App\Infrastructure\Address\Repositories;

use App\Domain\Address\Entities\Address;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Infrastructure\Address\Mappers\AddressMapper;
use App\Infrastructure\Address\Models\AddressModel;

class EloquentAddressRepository implements AddressRepositoryInterface
{
    public function findById(int $id): ?Address
    {
        $model = AddressModel::find($id);
        return $model ? AddressMapper::toDomain($model) : null;
    }

    public function save(Address $entity)
    {
        $data = AddressMapper::toArray($entity);
        
        if ($entity->getId()) {
            $model = AddressModel::find($entity->getId());
            if ($model) {
                $model->update($data);
                return;
            }
        }

        $model = new AddressModel($data);
        $model->save();
        $entity->setId($model->id);
        return $model->id;
    }

    public function delete(int $id): void
    {
        AddressModel::destroy($id);
    }

    public function exists(int $id): bool
    {
        return AddressModel::where('id', $id)->exists();
    }
}