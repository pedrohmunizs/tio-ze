<?php

namespace App\Application\VehicleDocument\UseCases;

use App\Domain\VehicleDocument\Entities\VehicleDocument;
use App\Domain\VehicleDocument\Repositories\VehicleDocumentRepositoryInterface;
use App\Application\VehicleDocument\DTOs\CreateVehicleDocumentData;
use App\Domain\VehicleDocument\Enums\VehicleDocumentType;
use App\Services\S3Service;

class CreateVehicleDocumentUseCase
{
    public function __construct(
        private VehicleDocumentRepositoryInterface $repository,
        private S3Service $s3Service
    ) {}

    public function execute(CreateVehicleDocumentData $data): VehicleDocument
    {
        $file_path = $this->s3Service->upload($data->file, 'vehicle-documents');

        $entity = new VehicleDocument(
            fk_vehicle: $data->fk_vehicle,
            file_path: $file_path,
            type: VehicleDocumentType::from($data->type)
        );

        $this->repository->save($entity);
        return $entity;
    }
}