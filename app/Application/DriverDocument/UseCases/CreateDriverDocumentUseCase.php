<?php

namespace App\Application\DriverDocument\UseCases;

use App\Domain\DriverDocument\Entities\DriverDocument;
use App\Domain\DriverDocument\Repositories\DriverDocumentRepositoryInterface;
use App\Application\DriverDocument\DTOs\CreateDriverDocumentData;
use App\Domain\DriverDocument\Enums\DriverDocumentType;
use App\Services\S3Service;

class CreateDriverDocumentUseCase
{
    public function __construct(
        private DriverDocumentRepositoryInterface $repository,
        private S3Service $s3Service
    ) {}

    public function execute(CreateDriverDocumentData $data): DriverDocument
    {
        $fk_driver = $data->fk_driver;

        if (!$fk_driver) {
            $driver = user()->driver;

            if (!$driver) {
                throw new \DomainException('Role not permitted');
            }

            $fk_driver = $driver->id;
        }

        $file_path = $this->s3Service->upload($data->file, 'driver-documents');

        $entity = new DriverDocument(
            fk_driver: $fk_driver,
            file_path: $file_path,
            valid_until: $data->valid_until->format('Y-m-d'),
            type: DriverDocumentType::from($data->type)
        );

        $this->repository->save($entity);
        return $entity;
    }
}