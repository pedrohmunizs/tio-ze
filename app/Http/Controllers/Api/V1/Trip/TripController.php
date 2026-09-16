<?php

namespace App\Http\Controllers\Api\V1\Trip;

use App\Http\Controllers\Controller;
use App\Application\Trip\UseCases\CreateTripUseCase;
use App\Application\Trip\UseCases\UpdateTripUseCase;
use App\Application\Trip\UseCases\DeleteTripUseCase;
use App\Application\Trip\UseCases\GetTripUseCase;
use App\Application\Trip\DTOs\CreateTripData;
use App\Application\Trip\UseCases\GenerateWeeklyTripUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function __construct(
        private CreateTripUseCase $createUseCase,
        private UpdateTripUseCase $updateUseCase,
        private DeleteTripUseCase $deleteUseCase,
        private GetTripUseCase $getUseCase,
        private GenerateWeeklyTripUseCase $generate_use_case,
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
            return response()->json(['message' => 'Trip not found'], 404);
        }

        return response()->json($entity->toArray());
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(CreateTripData::rules(), CreateTripData::messages());
        $data = CreateTripData::fromRequest($request);
        $entity = $this->createUseCase->execute($data);

        return response()->json($entity->toArray(), 201);
    }

    // public function update(Request $request, int $id): JsonResponse
    // {
    //     $data = TripData::fromRequest($request);
        
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

    public function generate(): JsonResponse
    {
        try {
            $data = $this->generate_use_case->execute();
            return response()->json($data, 201);

        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}