<?php

namespace App\Application\School\UseCases;

use App\Application\School\DTOs\CreateSchoolData;
use App\Domain\Address\Entities\Address;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Domain\School\Entities\School;
use App\Domain\School\Repositories\SchoolRepositoryInterface;

class CreateSchoolUseCase
{
    public function __construct(
        private SchoolRepositoryInterface $repository,
        private AddressRepositoryInterface $addressRepository,
    ) {}

    public function execute(CreateSchoolData $data): School
    {
        $address_id = $this->processAddress($data);

        $entity = new School(
            name: $data->name,
            phone: $data->phone,
            photo: null,
            addressId: $address_id
        );

        $this->repository->save($entity);
        return $entity;
    }

    private function processAddress(CreateSchoolData $data): ?int
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