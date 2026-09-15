<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Application\Driver\DTOs\ChangeStatusDriverData;
use App\Application\Driver\DTOs\UpdateAddressDriverData;
use App\Http\Controllers\Controller;
use App\Application\Driver\UseCases\UpdateDriverUseCase;
use App\Application\Driver\UseCases\DeleteDriverUseCase;
use App\Application\Driver\UseCases\GetDriverUseCase;
use App\Application\Driver\DTOs\UpdateDriverData;
use App\Application\Driver\UseCases\ChangeStatusDriverUseCase;
use App\Application\Driver\UseCases\UpdateAddressDriverUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function __construct(
        private UpdateDriverUseCase $updateUseCase,
        private DeleteDriverUseCase $deleteUseCase,
        private GetDriverUseCase $getUseCase,
        private ChangeStatusDriverUseCase $changeStatusUseCase,
        private UpdateAddressDriverUseCase $update_address_use_case,
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
            return response()->json(['message' => 'Driver not found'], 404);
        }

        return response()->json($entity->toArray());
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = UpdateDriverData::fromRequest($request);
        
        try {
            $entity = $this->updateUseCase->execute($id, $data);
            return response()->json($entity->toArray());
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function updateAddress(Request $request, int $id) : JsonResponse
    {
        $data = UpdateAddressDriverData::fromRequest($request);
        
        try {
            $entity = $this->update_address_use_case->execute($id, $data);
            return response()->json($entity->getRoutes());
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function changeStatus(int $id, Request $request): JsonResponse
    {
        $data = ChangeStatusDriverData::fromRequest($request);
        
        try {
            $entity = $this->changeStatusUseCase->execute($id, $data);
            return response()->json($entity->toArray());
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

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