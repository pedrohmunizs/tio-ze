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
        if (!$this->schoolRepository->findById($data->school_id)) {
            throw new \DomainException('School not found');
        }

        $provider_id = user()->provider->id;

        if ($this->repository->existsByProviderAndName($provider_id, $data->name)) {
            throw new \DomainException('Route already exists for this provider');
        }

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
            driverId: $data->driver_id,
        );

        $this->repository->save($entity);
        return $entity;
    }
}