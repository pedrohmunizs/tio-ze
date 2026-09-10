<?php

namespace App\Http\Controllers\Api\V1\VehicleDocument;

use App\Http\Controllers\Controller;
use App\Application\VehicleDocument\UseCases\CreateVehicleDocumentUseCase;
use App\Application\VehicleDocument\UseCases\UpdateVehicleDocumentUseCase;
use App\Application\VehicleDocument\UseCases\DeleteVehicleDocumentUseCase;
use App\Application\VehicleDocument\UseCases\GetVehicleDocumentUseCase;
use App\Application\VehicleDocument\DTOs\CreateVehicleDocumentData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleDocumentController extends Controller
{
    public function __construct(
        private CreateVehicleDocumentUseCase $createUseCase,
        private UpdateVehicleDocumentUseCase $updateUseCase,
        private DeleteVehicleDocumentUseCase $deleteUseCase,
        private GetVehicleDocumentUseCase $getUseCase,
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
        $data = $this->getUseCase->execute($id);

        if (!$data) {
            return response()->json(['message' => 'VehicleDocument not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(
            CreateVehicleDocumentData::rules(),
            CreateVehicleDocumentData::messages()
        );

        $data = CreateVehicleDocumentData::fromRequest($request);

        // return response()->json($request, 201);
        
        $entity = $this->createUseCase->execute($data);

        return response()->json($entity->toArray(), 201);
    }

    // public function update(Request $request, int $id): JsonResponse
    // {
    //     $data = VehicleDocumentData::fromRequest($request);
        
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