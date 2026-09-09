<?php

namespace App\Application\Vehicle\UseCases;

use App\Domain\Vehicle\Entities\Vehicle;
use App\Domain\Vehicle\Repositories\VehicleRepositoryInterface;
use App\Application\Vehicle\DTOs\CreateVehicleData;

class CreateVehicleUseCase
{
    public function __construct(
        private VehicleRepositoryInterface $repository
    ) {}

    public function execute(CreateVehicleData $data): Vehicle
    {
        $provider = user()->provider;
        
        if (!$provider) {
            throw new \DomainException('Usuário não é um prestador');
        }

        $entity = new Vehicle(
            brand: $data->brand,
            model: $data->model,
            plate: $data->plate,
            year: $data->year,
            capacity: $data->capacity,
            fk_provider: $provider->id
        );

        $this->repository->save($entity);
        return $entity;
    }
}