<?php

namespace App\Http\Controllers\Api\V1\TransportRequest;

use App\Http\Controllers\Controller;
use App\Application\TransportRequest\UseCases\CreateTransportRequestUseCase;
use App\Application\TransportRequest\UseCases\UpdateTransportRequestUseCase;
use App\Application\TransportRequest\UseCases\DeleteTransportRequestUseCase;
use App\Application\TransportRequest\UseCases\GetTransportRequestUseCase;
use App\Application\TransportRequest\DTOs\CreateTransportRequestData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransportRequestController extends Controller
{
    public function __construct(
        private CreateTransportRequestUseCase $createUseCase,
        private UpdateTransportRequestUseCase $updateUseCase,
        private DeleteTransportRequestUseCase $deleteUseCase,
        private GetTransportRequestUseCase $getUseCase,
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
            return response()->json(['message' => 'TransportRequest not found'], 404);
        }

        return response()->json($entity->toArray());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate(
            CreateTransportRequestData::rules(),
            CreateTransportRequestData::messages()
        );

        $data = CreateTransportRequestData::fromRequest($request);
        $entity = $this->createUseCase->execute($data);

        return response()->json($entity->toArray(), 201);
    }

    // public function update(Request $request, int $id): JsonResponse
    // {
    //     $data = TransportRequestData::fromRequest($request);
        
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