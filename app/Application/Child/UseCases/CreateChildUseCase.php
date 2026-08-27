<?php

namespace App\Application\Child\UseCases;

use App\Domain\Child\Entities\Child;
use App\Domain\Child\Repositories\ChildRepositoryInterface;
use App\Application\Child\DTOs\CreateChildData;
use App\Domain\Address\Entities\Address;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Infrastructure\User\Models\UserModel;

class CreateChildUseCase
{
    public function __construct(
        private ChildRepositoryInterface $repository,
        private AddressRepositoryInterface $addressRepository,
    ) {}

    public function execute(CreateChildData $data): Child
    {
        $parent_id = $data->fk_parent;

        if ($parent_id) {
            $parent = UserModel::find($parent_id);

            if (!$parent) {
                throw new \DomainException('Parent not found');
            }

            if (!$parent->hasRole('parent')) {
                throw new \DomainException('User is not a parent');
            }
            
        } else {
            if (!user()->hasRole('parent')) {
                throw new \DomainException('Role not permitted');
            }

            $parent_id = user()->id;
        }

        $address_id = $this->processAddress($data);

        $entity = new Child(
            name: $data->name,
            phone: $data->phone,
            grade: $data->grade,
            addressId: $address_id,
            parentId: $parent_id,
            schoolId: $data->fk_school,
        );
        
        $this->repository->save($entity);
        return $entity;
    }

    private function processAddress(CreateChildData $data): ?int
    {
        if (!$data->zip_code) {
            return null;
        }

        $address = new Address(
            zipCode: $data->zip_code,
            street: $data->street,
            number: $data->number,
            complement: $data->complement,
            neighborhood: $data->neighborhood,
            city: $data->city,
            state: $data->state,
        );

        return $this->addressRepository->save($address);
    }
}