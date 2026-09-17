<?php

namespace App\Application\Trip\UseCases;

use App\Application\Trip\DTOs\CreateTripData;
use App\Application\TripStudent\DTOs\CreateTripStudentData;
use App\Application\TripStudent\UseCases\CreateTripStudentUseCase;
use App\Domain\Child\Repositories\ChildRepositoryInterface;
use App\Domain\Route\Repositories\RouteRepositoryInterface;
use App\Domain\Trip\Enums\TripType;
use App\Domain\Trip\Repositories\TripRepositoryInterface;
use Carbon\Carbon;

class GenerateWeeklyTripUseCase
{
    public function __construct(
        private TripRepositoryInterface $trip_repository,
        private RouteRepositoryInterface $route_repository,
        private ChildRepositoryInterface $child_repository,
        private CreateTripUseCase $create_trip_use_case,
        private CreateTripStudentUseCase $create_trip_student_use_case,
    ) {}

    public function execute()
    {
        $routes = $this->route_repository->findManyByField('status', 'active');

        foreach ($routes as $route) {
            $tripDates = $this->getNextWeekDates($route['days_of_week']);
            $children = $this->child_repository->findByRouteId($route['id']);

            foreach ($tripDates as $date) {
                $exists = $this->trip_repository->existsByRouteAndDate($route['id'], $date->format('Y-m-d'));

                if ($exists) {
                    continue;
                }

                $types = ['going', 'returning'];

                foreach ($types as $type) {
                    $trip_dto = new CreateTripData(
                        fk_route: $route['id'],
                        fk_vehicle: $route['fk_vehicle'],
                        fk_driver: $route['fk_driver'],
                        date: $date,
                        type: TripType::from($type)
                    );

                    $trip = $this->create_trip_use_case->execute($trip_dto);

                    foreach ($children as $child) {
                        $trip_student_dto = new CreateTripStudentData(
                            fk_trip: $trip->getId(),
                            fk_student: $child->getId(),
                        );

                        $this->create_trip_student_use_case->execute($trip_student_dto);
                    }
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