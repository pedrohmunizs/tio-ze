<?php

namespace App\Application\School\UseCases;

use App\Application\School\DTOs\CreateSchoolData;
use App\Domain\Address\Services\AddressService;
use App\Domain\School\Entities\School;
use App\Domain\School\Repositories\SchoolRepositoryInterface;

class CreateSchoolUseCase
{
    public function __construct(
        private SchoolRepositoryInterface $repository,
        private AddressService $address_service,
    ) {}

    public function execute(CreateSchoolData $data): School
    {
        $address_id = $this->address_service->createAddressFromData(
            $data->zip_code,
            $data->street,
            $data->number,
            $data->neighborhood,
            $data->city,
            $data->state,
            $data->complement,
        );

        $entity = new School(
            name: $data->name,
            phone: $data->phone,
            addressId: $address_id
        );

        $this->repository->save($entity);
        return $entity;
    }
}