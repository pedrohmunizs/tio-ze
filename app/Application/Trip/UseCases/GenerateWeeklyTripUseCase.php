<?php

namespace App\Application\Trip\UseCases;

use App\Domain\Route\Repositories\RouteRepositoryInterface;
use App\Domain\Trip\Entities\Trip;
use App\Domain\Trip\Enums\TripType;
use App\Domain\Trip\Repositories\TripRepositoryInterface;
use Carbon\Carbon;

class GenerateWeeklyTripUseCase
{
    public function __construct(
        private TripRepositoryInterface $repository,
        private RouteRepositoryInterface $route_repository,
    ) {}

    public function execute()
    {
        $routes = $this->route_repository->findManyByField('status', 'active');

        
        foreach ($routes as $route) {
            // return $route;
            $tripDates = $this->getNextWeekDates($route['days_of_week']);

            foreach ($tripDates as $date) {
                $exists = $this->repository->existsByRouteAndDate($route['id'], $date->format('Y-m-d'));

                if ($exists) {
                    continue;
                }

                $types = ['going', 'returning'];

                foreach ($types as $type) {
                    // return $route;
                    $trip = new Trip(
                        fk_route: $route['id'],
                        fk_vehicle: $route['fk_vehicle'],
                        fk_driver: $route['fk_driver'],
                        date: $date,
                        type: TripType::from($type)
                    );

                    // return $trip;

                    $this->repository->save($trip);
                }
            }
        }
    }

    private function getNextWeekDates(array $daysOfWeek): array
    {
        $dates = [];
        $startOfWeek = Carbon::now()->next(Carbon::MONDAY);

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dayName = strtoupper($date->format('D'));

            if (in_array($dayName, $daysOfWeek)) {
                $dates[] = $date;
            }
        }

        return $dates;
    }
}