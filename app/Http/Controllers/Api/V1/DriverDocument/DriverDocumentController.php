<?php

namespace App\Http\Controllers\Api\V1\DriverDocument;

use App\Http\Controllers\Controller;
use App\Application\DriverDocument\UseCases\CreateDriverDocumentUseCase;
use App\Application\DriverDocument\UseCases\UpdateDriverDocumentUseCase;
use App\Application\DriverDocument\UseCases\DeleteDriverDocumentUseCase;
use App\Application\DriverDocument\UseCases\GetDriverDocumentUseCase;
use App\Application\DriverDocument\DTOs\CreateDriverDocumentData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverDocumentController extends Controller
{
    public function __construct(
        private CreateDriverDocumentUseCase $createUseCase,
        private DeleteDriverDocumentUseCase $deleteUseCase,
        private GetDriverDocumentUseCase $getUseCase,
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
            return response()->json(['message' => 'DriverDocument not found'], 404);
        }

        return response()->json($entity);
    }

    public function store(Request $request): JsonResponse
    {
        $data = CreateDriverDocumentData::fromRequest($request);
        $entity = $this->createUseCase->execute($data);

        return response()->json($entity->toArray(), 201);
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