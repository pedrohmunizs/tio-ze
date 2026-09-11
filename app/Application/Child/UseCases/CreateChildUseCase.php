<?php

namespace App\Application\Child\UseCases;

use App\Domain\Child\Entities\Child;
use App\Domain\Child\Repositories\ChildRepositoryInterface;
use App\Application\Child\DTOs\CreateChildData;
use App\Domain\Address\Services\AddressService;
use App\Domain\School\Repositories\SchoolRepositoryInterface;
use App\Infrastructure\User\Models\UserModel;

class CreateChildUseCase
{
    public function __construct(
        private ChildRepositoryInterface $repository,
        private AddressService $address_service,
        private SchoolRepositoryInterface $schoolRepository,
    ) {}

    public function execute(CreateChildData $data): Child
    {
        $parent_id = $this->resolveProviderId($data->fk_parent);
        $this->validateSchool($data->fk_school);

        $address_id = $this->address_service->createAddressFromData(
            $data->zip_code,
            $data->street,
            $data->number,
            $data->neighborhood,
            $data->city,
            $data->state,
            $data->complement,
        );

        $entity = new Child(
            name: $data->name,
            phone: $data->phone,
            grade: $data->grade,
            addressId: $address_id,
            parentId: $parent_id,
            schoolId: $data->fk_school,
        );
        
        $this->repository->save($entity);
        return $entity;
    }

    private function resolveProviderId(int $parent_id): int
    {
        if ($parent_id) {
            $parent = UserModel::find($parent_id);

            if (!$parent) {
                throw new \DomainException('Parent not found');
            }

            if (!$parent->hasRole('parent')) {
                throw new \DomainException('User is not a parent');
            }
            
        } else {
            if (!user()->hasRole('parent')) {
                throw new \DomainException('Role not permitted');
            }

            $parent_id = user()->id;
        }

        return $parent_id;
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
}