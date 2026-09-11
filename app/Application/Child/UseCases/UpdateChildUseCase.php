<?php

namespace App\Application\Child\UseCases;

use App\Domain\Child\Entities\Child;
use App\Domain\Child\Repositories\ChildRepositoryInterface;
use App\Application\Child\DTOs\UpdateChildData;
use App\Domain\Address\Services\AddressService;
use App\Domain\School\Repositories\SchoolRepositoryInterface;

class UpdateChildUseCase
{
    public function __construct(
        private ChildRepositoryInterface $repository,
        private AddressService $address_service,
        private SchoolRepositoryInterface $schoolRepository,
    ) {}

    public function execute(int $id, UpdateChildData $data): Child
    {
        $entity = $this->repository->findById($id);

        if (!$entity) {
            throw new \RuntimeException('Child not found');
        }

        $this->validateSchool($data->fk_school);
        $address_id = $this->updateAddress($data, $entity);
        
        $entity->update(
            name: $data->name,
            phone: $data->phone,
            grade: $data->grade,
            addressId: $address_id,
            schoolId: $data->fk_school,
        );

        $this->repository->save($entity);
        return $entity;
    }

    private function validateSchool(int $school_id): void
    {
        $school = $this->schoolRepository->findById($school_id);

        if (!$school) {
            throw new \DomainException('School not found');
        }

        if (!$school->isActive()) {
            throw new \DomainException('A escola não está ativa.');
        }
    }

    private function updateAddress(UpdateChildData $data, Child $child): int
    {
        $address_id = $child->getAddressId();
        $child_address = $child->getAddress();

        $dto_zip_code = preg_replace('/[^0-9]/', '', $data->zip_code);

        $zip_code_changed = $dto_zip_code && $child_address->getZipCode() !== $dto_zip_code;
        $number_changed = $data->number && $child_address->getNumber() !== $data->number;

        if (!$zip_code_changed && !$number_changed) {
            return $address_id;
        }

        $this->address_service->delete($address_id);
        
        $address_id = $this->address_service->createAddressFromData(
            $data->zip_code,
            $data->street,
            $data->number,
            $data->neighborhood,
            $data->city,
            $data->state,
            $data->complement,
        );

        return $address_id;
    }
}