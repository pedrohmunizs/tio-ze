<?php

namespace App\Http\Controllers\Api\V1\Provider;

use App\Http\Controllers\Controller;
use App\Application\Provider\UseCases\CreateProviderUseCase;
use App\Application\Provider\UseCases\UpdateProviderUseCase;
use App\Application\Provider\UseCases\DeleteProviderUseCase;
use App\Application\Provider\UseCases\GetProviderUseCase;
use App\Application\Provider\DTOs\ProviderData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function __construct(
        private CreateProviderUseCase $createUseCase,
        private UpdateProviderUseCase $updateUseCase,
        private DeleteProviderUseCase $deleteUseCase,
        private GetProviderUseCase $getUseCase,
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
            return response()->json(['message' => 'Provider not found'], 404);
        }

        return response()->json($entity->toArray());
    }

    public function store(Request $request): JsonResponse
    {
        $data = ProviderData::fromRequest($request);
        $entity = $this->createUseCase->execute($data);

        return response()->json($entity->toArray(), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = ProviderData::fromRequest($request);
        
        try {
            $entity = $this->updateUseCase->execute($id, $data);
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