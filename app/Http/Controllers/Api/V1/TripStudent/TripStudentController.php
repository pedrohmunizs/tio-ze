<?php

namespace App\Http\Controllers\Api\V1\TripStudent;

use App\Http\Controllers\Controller;
use App\Application\TripStudent\UseCases\CreateTripStudentUseCase;
use App\Application\TripStudent\UseCases\UpdateTripStudentUseCase;
use App\Application\TripStudent\UseCases\DeleteTripStudentUseCase;
use App\Application\TripStudent\UseCases\GetTripStudentUseCase;
use App\Application\TripStudent\DTOs\CreateTripStudentData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TripStudentController extends Controller
{
    public function __construct(
        private CreateTripStudentUseCase $createUseCase,
        private UpdateTripStudentUseCase $updateUseCase,
        private DeleteTripStudentUseCase $deleteUseCase,
        private GetTripStudentUseCase $getUseCase,
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
            return response()->json(['message' => 'TripStudent not found'], 404);
        }

        return response()->json($entity->toArray());
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(CreateTripStudentData::rules(), CreateTripStudentData::messages());
        $data = CreateTripStudentData::fromRequest($request);
        $entity = $this->createUseCase->execute($data);

        return response()->json($entity->toArray(), 201);
    }

    // public function update(Request $request, int $id): JsonResponse
    // {
    //     $data = TripStudentData::fromRequest($request);
        
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
}