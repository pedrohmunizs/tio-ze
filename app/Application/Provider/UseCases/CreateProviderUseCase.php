<?php

namespace App\Application\Provider\UseCases;

use App\Application\Provider\DTOs\CreateProviderData;
use App\Domain\Address\Services\AddressService;
use App\Domain\Provider\Entities\Provider;
use App\Domain\Provider\Repositories\ProviderRepositoryInterface;

class CreateProviderUseCase
{
    public function __construct(
        private ProviderRepositoryInterface $repository,
        private AddressService $address_service,
    ) {}

    public function execute(CreateProviderData $data): Provider
    {
        $fk_address = $this->address_service->createAddressFromData(
            $data->zip_code,
            $data->street,
            $data->number,
            $data->neighborhood,
            $data->city,
            $data->state,
            $data->complement,
        );
        
        $entity = new Provider(
            name: $data->name,
            phone: $data->phone,
            userId: $data->fk_user,
            addressId: $fk_address,
            isAutonomous: $data->is_autonomous
        );

        $this->repository->save($entity);
        return $entity;
    }
}