<?php

namespace App\Application\Route\UseCases;

use App\Domain\Route\Repositories\RouteRepositoryInterface;
use App\Domain\Stop\Repositories\StopRepositoryInterface;
use App\Infrastructure\Routing\Services\OsrmService;

class OptimizePickupRouteUseCase
{
    public function __construct(
        private RouteRepositoryInterface $routeRepository,
        private StopRepositoryInterface $stopRepository,
        private OsrmService $osrmService,
    ) {}

    public function execute(int $fk_route, string $type = 'going'): array
    {
        $route = $this->routeRepository->findById($fk_route);
        
        if (!$route) {
            throw new \DomainException('Rota não encontrada');
        }

        $driver = $route->getDriver();
        $garage = [
            'latitude' => $driver->getUser()->getAddress()->getLatitude(),
            'longitude' => $driver->getUser()->getAddress()->getLongitude(),
        ];

        $stops = $this->stopRepository->findByRouteId($fk_route, $type);
        $students = [];

        foreach ($stops as $stop) {
            $student = $stop['student'];

            if (!$student) {
                continue;
            }

            $address = $stop['address'];
            
            if (!$address) {
                continue;
            }
            
            $latitude = $address['latitude'];
            $longitude = $address['longitude'];
            
            if ($latitude === null || $longitude === null) {
                continue;
            }
            
            $students[] = [
                'id' => $student['id'],
                'name' => $student['name'],
                'address' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ],
                'stop_id' => $stop['id'],
            ];
        }

        if (count($students) < 2) {
            return [
                'message' => 'Poucos alunos para otimizar',
                'students' => $students,
            ];
        }

        $school = $route->getSchool()->toArray();
        
        $schoolLocation = [
            'latitude' => $school['address']['latitude'],
            'longitude' => $school['address']['longitude'],
        ];

        $optimized = $this->osrmService->getOptimizedPickupRoute($garage, $students, $schoolLocation, $type);

        if (empty($optimized)) {
            throw new \DomainException('Não foi possível otimizar a rota');
        }

        foreach ($optimized['students'] as $student) {
            $this->stopRepository->updateOrder($student['stop_id'], $student['order']);
        }

        return $optimized;
    }
}