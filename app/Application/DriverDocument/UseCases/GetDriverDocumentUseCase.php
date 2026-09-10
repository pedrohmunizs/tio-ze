<?php

namespace App\Application\DriverDocument\UseCases;

use App\Domain\DriverDocument\Repositories\DriverDocumentRepositoryInterface;
use App\Infrastructure\DriverDocument\Mappers\DriverDocumentMapper;
use App\Services\S3Service;

class GetDriverDocumentUseCase
{
    public function __construct(
        private DriverDocumentRepositoryInterface $repository,
        private S3Service $s3Service,
    ) {}

    public function execute(int $id): ?array
    {
        $document = $this->repository->findById($id);

        if (!$document) {
            return null;
        }

        $temporaryUrl = null;
        if ($document->getFilePath()) {
            $temporaryUrl = $this->s3Service->getTemporaryUrl($document->getFilePath(), 30);
        }

        return DriverDocumentMapper::toArray($document, $temporaryUrl);
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findAll($filters, $page, $perPage);
    }
}