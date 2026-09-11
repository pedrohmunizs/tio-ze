<?php

namespace App\Http\Controllers\Api\V1\Route;

use App\Application\Route\DTOs\CreateRouteData;
use App\Http\Controllers\Controller;
use App\Application\Route\UseCases\CreateRouteUseCase;
use App\Application\Route\UseCases\UpdateRouteUseCase;
use App\Application\Route\UseCases\DeleteRouteUseCase;
use App\Application\Route\UseCases\GetRouteUseCase;
use App\Application\Route\UseCases\OptimizePickupRouteUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function __construct(
        private CreateRouteUseCase $createUseCase,
        private UpdateRouteUseCase $updateUseCase,
        private DeleteRouteUseCase $deleteUseCase,
        private GetRouteUseCase $getUseCase,
        private OptimizePickupRouteUseCase $optimize_route_use_case,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'search']);
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);

        $result = $this->getUseCase->list($filters, $page, $perPage);

        return response()->json($result);
    }

    public function show(int $id): JsonResponse
    {
        $entity = $this->getUseCase->execute($id);

        if (!$entity) {
            return response()->json(['message' => 'Route not found'], 404);
        }

        return response()->json($entity->toArray());
    }

    public function store(Request $request): JsonResponse
    {
        $data = CreateRouteData::fromRequest($request);
        $entity = $this->createUseCase->execute($data);

        return response()->json($entity->toArray(), 201);
    }

    // public function update(Request $request, int $id): JsonResponse
    // {
    //     $data = RouteData::fromRequest($request);
        
    //     try {
    //         $entity = $this->updateUseCase->execute($id, $data);
    //         return response()->json($entity->toArray());
    //     } catch (\RuntimeException $e) {
    //         return response()->json(['message' => $e->getMessage()], 404);
    //     }
    // }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->deleteUseCase->execute($id);
            return response()->json(null, 204);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function optimize(int $routeId)
    {
        try {
            $types = ['going', 'returning'];

            foreach ($types as $type) {
                $this->optimize_route_use_case->execute($routeId, $type);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}