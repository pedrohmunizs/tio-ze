<?php

namespace App\Application\Driver\UseCases;

use App\Application\Driver\DTOs\UpdateAddressDriverData;
use App\Application\Route\UseCases\OptimizePickupRouteUseCase;
use App\Domain\Address\Services\AddressService;
use App\Domain\Driver\Entities\Driver;
use App\Domain\Driver\Repositories\DriverRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;

class UpdateAddressDriverUseCase
{
    public function __construct(
        private DriverRepositoryInterface $repository,
        private UserRepositoryInterface $user_repository,
        private AddressService $address_service,
        private OptimizePickupRouteUseCase $optimize_route_use_case,
    ) {}

    public function execute(int $id, UpdateAddressDriverData $data): Driver
    {
        $entity = $this->repository->findById($id);

        if (!$entity) {
            throw new \RuntimeException('Driver not found');
        }

        $user = $entity->getUser();

        $address_id = $user->getAddressId();
        $child_address = $user->getAddress();

        $dto_zip_code = preg_replace('/[^0-9]/', '', $data->zip_code);

        $zip_code_changed = $dto_zip_code && $child_address->getZipCode() !== $dto_zip_code;
        $number_changed = $data->number && $child_address->getNumber() !== $data->number;

        if (!$zip_code_changed && !$number_changed) {
            return $entity;
        }

        $this->address_service->delete($address_id);
        
        $address_id = $this->address_service->createAddressFromData(
            $data->zip_code,
            $data->street,
            $data->number,
            $data->neighborhood,
            $data->city,
            $data->state,
            $data->complement,
        );

        $user->setAddressId($address_id);
        $this->user_repository->save($user);

        $routes = $entity->getRoutes();

        foreach ($routes as $route) {
            $types = ['going', 'returning'];

            foreach ($types as $type) {
                $this->optimize_route_use_case->execute($route['id'], $type);
            }
        }

        return $entity;
    }
}