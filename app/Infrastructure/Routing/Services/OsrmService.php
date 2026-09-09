<?php

namespace App\Infrastructure\Routing\Services;

use Illuminate\Support\Facades\Http;

class OsrmService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://router.project-osrm.org';
    }

    /**
     * Calcula a rota otimizada (TSP) entre múltiplos pontos
     * 
     * @param array $points Lista de pontos com 'latitude' e 'longitude'
     * @param string $start Índice do ponto inicial (opcional)
     * @param string $end Índice do ponto final (opcional)
     * @return array|null
     */
    public function getOptimizedTrip(array $points): ?array
    {
        if (count($points) < 2) {
            return null;
        }

        $coordinates = implode(';', array_map(function ($point) {
            return "{$point['longitude']},{$point['latitude']}";
        }, $points));

        $params = [
            'annotations' => 'distance,duration',
            'overview' => 'full',
            'geometries' => 'geojson',
        ];

        $url = "{$this->baseUrl}/trip/v1/driving/{$coordinates}";

        $response = Http::get($url, $params);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        if ($data['code'] !== 'Ok') {
            return null;
        }

        return $data;
    }

    public function getOptimizedPickupRoute(array $garage, array $students, array $school, string $type): array
    {
        if ($type == 'going') {
            $points = [
                [
                    'latitude' => $garage['latitude'],
                    'longitude' => $garage['longitude'],
                    'type' => 'garage',
                    'name' => 'Garagem',
                ]
            ];
        } else {
            $points = [
                [
                    'latitude' => $school['latitude'],
                    'longitude' => $school['longitude'],
                    'type' => 'school',
                    'name' => 'Escola',
                ]
            ];
        }

        foreach ($students as $index => $student) {
            $points[] = [
                'latitude' => $student['address']['latitude'],
                'longitude' => $student['address']['longitude'],
                'type' => 'student',
                'name' => $student['name'],
                'student_id' => $student['id'],
                'original_index' => $index,
            ];
        }

        if ($type == 'going') {
            $points[] = [
                'latitude' => $school['latitude'],
                'longitude' => $school['longitude'],
                'type' => 'school',
                'name' => 'Escola',
            ];
        } else {
            $points[] = [
                'latitude' => $garage['latitude'],
                'longitude' => $garage['longitude'],
                'type' => 'garage',
                'name' => 'Garagem',
            ];
        }

        $trip = $this->getOptimizedTrip($points);

        if (!$trip) {
            return [
                'students' => array_map(function($student, $index) {
                    return [
                        'student_id' => $student['id'],
                        'name' => $student['name'],
                        'latitude' => $student['address']['latitude'],
                        'longitude' => $student['address']['longitude'],
                        'order' => $index + 1,
                    ];
                }, $students, array_keys($students)),
                'total_distance_meters' => 0,
                'total_distance_km' => 0,
                'total_duration_seconds' => 0,
                'total_duration_minutes' => 0,
                'route_geometry' => null,
                'waypoints' => [],
                'error' => 'OSRM falhou, usando ordem original',
            ];
        }

        $waypoints = $trip['waypoints'] ?? [];
        $order = [];

        foreach ($waypoints as $waypoint) {
            $index = $waypoint['waypoint_index'];
            $point = $points[$index] ?? null;

            if ($point && $point['type'] === 'student') {
                $order[] = [
                    'student_id' => $point['student_id'],
                    'name' => $point['name'],
                    'latitude' => $point['latitude'],
                    'longitude' => $point['longitude'],
                    'order' => count($order) + 1,
                ];
            }
        }

        $route = $trip['trips'][0] ?? null;

        return [
            'students' => $order,
            'total_distance_meters' => $route['distance'] ?? 0,
            'total_distance_km' => ($route['distance'] ?? 0) / 1000,
            'total_duration_seconds' => $route['duration'] ?? 0,
            'total_duration_minutes' => ($route['duration'] ?? 0) / 60,
            'route_geometry' => $route['geometry'] ?? null,
            'waypoints' => $waypoints,
        ];
    }
}