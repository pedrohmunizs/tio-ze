<?php

namespace App\Application\Route\UseCases;

use App\Domain\Route\Entities\Route;
use App\Domain\Route\Repositories\RouteRepositoryInterface;
use App\Application\Route\DTOs\CreateRouteData;
use App\Domain\Route\ValueObjects\DaysOfWeek;
use App\Domain\Route\ValueObjects\Price;
use App\Domain\Route\ValueObjects\Time;
use App\Domain\School\Repositories\SchoolRepositoryInterface;

class CreateRouteUseCase
{
    public function __construct(
        private RouteRepositoryInterface $repository,
        private SchoolRepositoryInterface $schoolRepository,
    ) {}

    public function execute(CreateRouteData $data): Route
    {
        $this->validateSchool($data->school_id);

        $provider_id = user()->provider->id;

        if ($this->repository->existsByProviderAndName($provider_id, $data->name)) {
            throw new \DomainException('Route already exists for this provider');
        }

        $fk_driver = $this->resolveDriverId($data);

        $price = new Price($data->price);
        $goingTime = new Time($data->going_time);
        $returningTime = new Time($data->returning_time);
        $daysOfWeek = new DaysOfWeek($data->days_of_week);

        $entity = new Route(
            name: $data->name,
            price: $price,
            goingTime: $goingTime,
            returningTime: $returningTime,
            daysOfWeek: $daysOfWeek,
            schoolId: $data->school_id,
            providerId: $provider_id,
            driverId: $fk_driver,
            vehicleId: $data->vehicle_id,
        );

        $this->repository->save($entity);
        return $entity;
    }

    private function resolveDriverId(CreateRouteData $data): int
    {
        $user = user();
        $provider = $user->provider;

        if (!$provider) {
            throw new \DomainException('Usuário não é um prestador');
        }

        if ($provider->is_autonomous) {
            $driver = $user->driver;
            
            if (!$driver) {
                throw new \DomainException('Motorista autônomo não possui perfil de motorista');
            }

            if ($driver->status != 'active') {
                throw new \DomainException('O motorista não está ativo.');
            }
            
            return $driver->id;
        }

        if (!$data->driver_id) {
            throw new \DomainException('É necessário informar um motorista para esta rota');
        }

        return $data->driver_id;
    }

    private function validateSchool(int $school_id): void
    {
        $school = $this->schoolRepository->findById($school_id);

        if (!$school) {
            throw new \DomainException('School not found');
        }

        if (!$school->isActive()) {
            throw new \DomainException('A escola não está ativa.');
        }
    }
}