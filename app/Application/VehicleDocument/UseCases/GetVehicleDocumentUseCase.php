<?php

namespace App\Application\VehicleDocument\UseCases;

use App\Domain\VehicleDocument\Entities\VehicleDocument;
use App\Domain\VehicleDocument\Repositories\VehicleDocumentRepositoryInterface;
use App\Infrastructure\VehicleDocument\Mappers\VehicleDocumentMapper;
use App\Services\S3Service;

class GetVehicleDocumentUseCase
{
    public function __construct(
        private VehicleDocumentRepositoryInterface $repository,
        private S3Service $s3Service,
    ) {}

    public function execute(int $id): ?array
    {
        $document = $this->repository->findById($id);
        
        if (!$document) {
            return null;
        }

        // 2. 👈 GERAR A URL TEMPORÁRIA AQUI
        $temporaryUrl = null;
        if ($document->getFilePath()) {
            $temporaryUrl = $this->s3Service->getTemporaryUrl(
                $document->getFilePath(),
                30 // 30 minutos
            );
        }

        // 3. Passar a URL para o Mapper
        return VehicleDocumentMapper::toArray($document, $temporaryUrl);
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findAll($filters, $page, $perPage);
    }
}