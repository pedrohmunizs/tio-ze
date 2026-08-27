<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Application\School\DTOs\CreateSchoolData;
use App\Http\Controllers\Controller;
use App\Application\School\UseCases\CreateSchoolUseCase;
use App\Application\School\UseCases\UpdateSchoolUseCase;
use App\Application\School\UseCases\DeleteSchoolUseCase;
use App\Application\School\UseCases\GetSchoolUseCase;
use App\Application\School\DTOs\SchoolData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function __construct(
        private CreateSchoolUseCase $createUseCase,
        private UpdateSchoolUseCase $updateUseCase,
        private DeleteSchoolUseCase $deleteUseCase,
        private GetSchoolUseCase $getUseCase,
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
            return response()->json(['message' => 'School not found'], 404);
        }

        return response()->json($entity->toArray());
    }

    public function store(Request $request): JsonResponse
    {
        $data = CreateSchoolData::fromRequest($request);
        $entity = $this->createUseCase->execute($data);

        return response()->json($entity->toArray(), 201);
    }

    // public function update(Request $request, int $id): JsonResponse
    // {
    //     $data = SchoolData::fromRequest($request);
        
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