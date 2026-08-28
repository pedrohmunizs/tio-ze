<?php

namespace App\Application\Provider\UseCases;

use App\Application\Provider\DTOs\CreateProviderData;
use App\Domain\Address\Entities\Address;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Domain\Provider\Entities\Provider;
use App\Domain\Provider\Repositories\ProviderRepositoryInterface;

class CreateProviderUseCase
{
    public function __construct(
        private ProviderRepositoryInterface $repository,
        private AddressRepositoryInterface $addressRepository,
    ) {}

    public function execute(CreateProviderData $data): Provider
    {
        $fk_address = $this->processAddress($data);
        
        $entity = new Provider(
            name: $data->name,
            phone: $data->phone,
            userId: $data->fk_user,
            addressId: $fk_address,
        );

        $this->repository->save($entity);
        return $entity;
    }

    private function processAddress(CreateProviderData $data): ?int
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